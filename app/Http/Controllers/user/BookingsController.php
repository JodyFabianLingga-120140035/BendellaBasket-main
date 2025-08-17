<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\BasketCourt;
use App\Models\PaymentMethod;
use App\Models\PaymentStatusHistory;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewPaidBookingNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Rules\Recaptcha;
use Illuminate\Support\Str;

class BookingsController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            abort(404);
        }

        if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2) {
            abort(404);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil booking aktif
        $activeBookings = Booking::with(['court', 'schedule'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'waiting_payment', 'paid'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil history booking
        $historyBookings = Booking::with(['court', 'schedule'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'waiting_payment', 'paid', 'completed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.bookings.index', [
            'activeBookings' => $activeBookings,
            'historyBookings' => $historyBookings,
            'activeTab' => $request->query('tab', 'active') // Default ke 'active' jika tidak ada parameter
        ]);
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        // Eager load relasi yang diperlukan
        $booking->load(['court', 'schedule', 'paymentHistory.paymentMethod']);

        return view('user.bookings.show', compact('booking'));
    }

    public function keranjang()
    {
        $bookings = Booking::with(['schedule.court', 'court'])
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Cek apakah semua booking sudah memiliki jadwal
        $canCheckout = $bookings->isNotEmpty() && $bookings->every(function ($booking) {
            return $booking->schedule_id !== null;
        });

        return view('user.bookings.keranjang', compact('bookings', 'canCheckout'));
    }

    public function destroy(Booking $booking)
    {
        try {
            DB::beginTransaction();
            
            // Ambil jadwal yang terkait dengan booking ini
            if ($booking->schedule) {
                // Hitung waktu mulai dan selesai berdasarkan durasi
                $startTime = Carbon::parse($booking->schedule->start_time);
                $endTime = $startTime->copy()->addHours($booking->duration);
                
                // Update semua jadwal dalam rentang waktu booking
                Schedule::where('court_id', $booking->court_id)
                    ->whereDate('schedule_date', $booking->schedule->schedule_date)
                    ->where('status', 'booked')
                    ->where(function($query) use ($startTime, $endTime) {
                        $query->where('start_time', '>=', $startTime)
                              ->where('start_time', '<', $endTime);
                    })
                    ->update([
                        'status' => 'available'
                    ]);
            }
            
            // Hapus booking
            $booking->delete();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Booking berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting booking:', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Gagal menghapus booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkout(Request $request)
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->get();

        // Validasi semua booking harus memiliki jadwal
        if ($bookings->contains('schedule_id', null)) {
            return redirect()->back()
                ->with('error', 'Silahkan pilih jadwal untuk semua lapangan sebelum melanjutkan pembayaran');
        }

        foreach ($bookings as $booking) {
            $booking->status = 'waiting_payment';
            $booking->save();
        }

        // Redirect langsung ke halaman pembayaran
        return redirect()->route('user.bookings.payment');
    }

    public function store(Request $request, BasketCourt $court)
    {
        // Generate booking code
        $bookingCode = 'BK-' . strtoupper(Str::random(8));
        
        // Buat booking baru dengan status pending
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'court_id' => $court->id,
            'status' => 'pending',
            'booking_code' => $bookingCode,
            'duration' => 1,
            'total_price' => $court->price_per_hour,
            'down_payment' => 0,
            'terms_accepted' => false,
        ]);

        return redirect()->route('user.bookings.keranjang')
            ->with('success', 'Lapangan berhasil ditambahkan ke keranjang');
    }

    public function getSchedules(Booking $booking, Request $request)
    {
        try {
            // Ambil tanggal dari request atau gunakan tanggal booking/hari ini
            $selectedDate = $request->date ? 
                Carbon::parse($request->date) : 
                ($booking->schedule ? $booking->schedule->schedule_date : now());
            
            // Generate tanggal untuk 7 hari ke depan
            $dates = collect(range(0, 6))->map(function ($i) use ($selectedDate) {
                $date = now()->addDays($i);
                return [
                    'value' => $date->format('Y-m-d'),
                    'day' => $date->isoFormat('ddd'),
                    'date' => $date->format('d'),
                    'month' => $date->isoFormat('MMM'),
                    'is_today' => $date->isToday()
                ];
            });

            // Ambil semua slot waktu yang tersedia untuk tanggal yang dipilih
            $schedules = Schedule::where('court_id', $booking->court_id)
                ->whereDate('schedule_date', $selectedDate)
                ->orderBy('start_time')
                ->get();

            $formattedSchedules = $schedules->map(function ($schedule) use ($booking) {
                return [
                    'id' => $schedule->id,
                    'time' => date('H:i:s', strtotime($schedule->start_time)),
                    'status' => $schedule->status === 'available' ? 'available' : $schedule->status
                ];
            });

            $response = [
                'schedules' => $formattedSchedules,
                'dates' => $dates,
                'selected_date' => $selectedDate->format('Y-m-d'),
                'price_per_hour' => $booking->court->price_per_hour,
                'current_schedule' => [
                    'date' => $selectedDate->format('Y-m-d'),
                    'start_time' => $booking->schedule ? 
                        date('H:i:s', strtotime($booking->schedule->start_time)) : 
                        null
                ]
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error in getSchedules:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat jadwal',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateSchedule(Request $request, Booking $booking)
    {
        $request->validate([
            'slots' => 'required|array|min:1',
            'slots.*.time' => 'required|date_format:H:i:s',
            'duration' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $firstSlot = $request->slots[0];
            $selectedDate = $request->date ?? now()->format('Y-m-d');

            // Validasi ketersediaan semua slot yang dipilih
            foreach ($request->slots as $slot) {
                $schedule = Schedule::where('court_id', $booking->court_id)
                    ->whereDate('schedule_date', $selectedDate)
                    ->whereTime('start_time', Carbon::parse($slot['time'])->format('H:i:s'))
                    ->first();

                if (!$schedule) {
                    throw new \Exception('Jadwal tidak tersedia');
                }

                if ($schedule->status === 'booked' && $schedule->id !== $booking->schedule_id) {
                    throw new \Exception('Jadwal sudah dibooking oleh orang lain');
                }
            }

            // Ambil jadwal pertama untuk referensi booking
            $schedule = Schedule::where('court_id', $booking->court_id)
                ->whereDate('schedule_date', $selectedDate)
                ->whereTime('start_time', Carbon::parse($firstSlot['time'])->format('H:i:s'))
                ->first();

            // Reset status jadwal lama jika ada
            if ($booking->schedule_id) {
                Schedule::where('court_id', $booking->court_id)
                    ->whereDate('schedule_date', $booking->schedule->schedule_date)
                    ->where('status', 'booked')
                    ->update(['status' => 'available']);
            }

            // Update status jadwal yang dipilih menjadi booked
            foreach ($request->slots as $slot) {
                $startTime = Carbon::parse($selectedDate . ' ' . $slot['time']);
                $endTime = $startTime->copy()->addHour();

                Schedule::where('court_id', $booking->court_id)
                    ->whereDate('schedule_date', $selectedDate)
                    ->whereTime('start_time', $startTime->format('H:i:s'))
                    ->update([
                        'status' => 'booked',
                        'start_time' => $startTime,
                        'end_time' => $endTime
                    ]);
            }

            // Update booking
            $totalPrice = $request->duration * $booking->court->price_per_hour;
            $booking->update([
                'schedule_id' => $schedule->id,
                'duration' => $request->duration,
                'total_price' => $totalPrice
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dibooking'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating schedule:', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function payment(Request $request)
    {
        $bookings = Booking::with(['court', 'schedule'])
            ->where('user_id', Auth::id())
            ->where('status', 'waiting_payment')
            ->whereNotNull('schedule_id') // Pastikan hanya booking dengan jadwal
            ->orderBy('created_at', 'desc')
            ->get();

        if ($bookings->isEmpty()) {
            return redirect()->route('user.bookings.index')
                ->with('error', 'Tidak ada booking yang menunggu pembayaran');
        }

        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $totalAmount = $bookings->sum('total_price');
        $downPaymentAmount = $totalAmount * 0.5; // 50% dari total

        return view('user.bookings.payment', compact('bookings', 'paymentMethods', 'totalAmount', 'downPaymentAmount'));
    }

    public function processPayment(Request $request)
    {
        try {
            \Log::info('Starting payment process', [
                'has_recaptcha' => $request->has('g-recaptcha-response'),
                'recaptcha_length' => strlen($request->input('g-recaptcha-response')),
            ]);

            // Validasi reCAPTCHA terlebih dahulu
            $recaptchaRule = new Recaptcha;
            $recaptchaResult = $recaptchaRule->validate(
                'g-recaptcha-response', 
                $request->input('g-recaptcha-response'),
                function($message) {
                    throw new \Exception($message);
                }
            );

            // Jika sampai di sini berarti reCAPTCHA valid, lanjutkan dengan validasi lainnya
            $validator = \Validator::make($request->all(), [
                'payment_method_id' => 'required|exists:payment_methods,id',
                'payment_type' => 'required|in:down_payment,full_payment',
                'payment_proof' => 'required|image|max:2048',
                'terms_accepted' => 'required|accepted'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $bookings = Booking::where('user_id', Auth::id())
                ->where('status', 'waiting_payment')
                ->get();

            if ($bookings->isEmpty()) {
                return redirect()->route('user.bookings.payment')
                    ->with('error', 'Tidak ada booking yang menunggu pembayaran');
            }

            $totalAmount = $bookings->sum('total_price');
            $amountPaid = $request->payment_type === 'down_payment' ? ($totalAmount * 0.5) : $totalAmount;

            // Upload bukti pembayaran
            $paymentProof = $request->file('payment_proof')->store('payment-proofs', 'public');

            // Buat payment history
            $paymentHistory = PaymentStatusHistory::create([
                'booking_id' => $bookings->first()->id,
                'payment_method_id' => $request->payment_method_id,
                'payment_type' => $request->payment_type,
                'amount_paid' => $amountPaid,
                'payment_status' => 'pending',
                'payment_proof' => $paymentProof
            ]);

            // Update status booking dan kirim email
            foreach ($bookings as $booking) {
                $booking->status = 'paid';
                if ($request->payment_type === 'down_payment') {
                    $booking->down_payment = $amountPaid;
                }
                $booking->terms_accepted = true;
                $booking->terms_accepted_at = now();
                $booking->save();

                // Kirim email ke semua admin (role_id = 1)
                try {
                    $admins = User::where('role_id', 1)->get();
                    if ($admins->isEmpty()) {
                        \Log::warning('Tidak ada admin ditemukan dengan role_id = 1');
                        return;
                    }
                    
                    \Log::info('Jumlah admin ditemukan: ' . $admins->count());
                    foreach ($admins as $admin) {
                        \Log::info('Mencoba mengirim email ke: ' . $admin->email);
                        try {
                            \Log::info('Konfigurasi SMTP: ' . json_encode([
                                'host' => config('mail.mailers.smtp.host'),
                                'port' => config('mail.mailers.smtp.port'),
                                'encryption' => config('mail.mailers.smtp.encryption'),
                                'username' => config('mail.mailers.smtp.username'),
                                'from' => config('mail.from.address'),
                            ]));
                            
                            // Tambahkan timeout lebih lama
                            Mail::to($admin->email)
                                ->send(new NewPaidBookingNotification($booking));

                            // Tunggu sebentar setelah mengirim email
                            sleep(2);
                            
                            \Log::info('Email berhasil dikirim ke: ' . $admin->email);
                        } catch (\Exception $emailError) {
                            \Log::error('Error saat mengirim ke ' . $admin->email . ': ' . $emailError->getMessage());
                            \Log::error('Stack trace email: ' . $emailError->getTraceAsString());
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error detail: ' . $e->getMessage());
                    \Log::error('File: ' . $e->getFile());
                    \Log::error('Line: ' . $e->getLine());
                }
            }

            return redirect()->route('user.bookings.index')
                ->with('success', 'Pembayaran berhasil diproses dan menunggu konfirmasi admin');

        } catch (\Exception $e) {
            \Log::error('Payment Processing Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function reschedule(Request $request, Booking $booking)
    {
        try {
            // Validasi status booking
            if ($booking->status !== 'paid') {
                return response()->json([
                    'message' => 'Hanya booking dengan status PAID yang dapat diubah jadwalnya'
                ], 422);
            }

            // Validasi kepemilikan booking
            if ($booking->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses untuk mengubah jadwal ini'
                ], 403);
            }

            $request->validate([
                'schedule_id' => 'required|exists:schedules,id'
            ]);

            // Ambil jadwal baru
            $newSchedule = Schedule::findOrFail($request->schedule_id);

            // Cek apakah jadwal tersedia
            if ($newSchedule->status === 'booked') {
                return response()->json([
                    'message' => 'Jadwal sudah tidak tersedia'
                ], 422);
            }

            DB::beginTransaction();
            try {
                // Update status jadwal lama menjadi available
                if ($booking->schedule) {
                    $booking->schedule->update(['status' => 'available']);
                }

                // Update status jadwal baru menjadi booked
                $newSchedule->update(['status' => 'booked']);

                // Update booking dengan jadwal baru
                $booking->update([
                    'schedule_id' => $newSchedule->id
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Jadwal booking berhasil diubah',
                    'booking' => $booking->load('schedule')
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Reschedule error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengubah jadwal'
            ], 500);
        }
    }
}
