<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\BasketCourt;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminBookingController extends Controller
{
    // untuk melakukan pengecekan login (sekuritas)
    public function __construct()
    {
        // untuk pengecekan login atau tidak.
        if (!Auth::check()) {
            abort(404);
        }

        // jika ada yang login bukan dengan role id 1 maka ditolak.
        if (Auth::user()->role_id != 1) {
            abort(404); 
        }
    }

    // untuk tampilan awal setelah login.
    public function index()
    {
        $data = [
            'title' => 'Booking Lapangan',
            'bookings' => Booking::with(['user', 'court', 'schedule', 'paymentHistory' => function($query) {
                $query->latest();
            }])->latest()->get(),
            'members' => User::where('is_member', true)->get(),
            'courts' => BasketCourt::where('is_available', true)->get()
        ];
        return view('admin.crud.booking', $data);
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,waiting_payment,paid,completed,cancelled'
        ]);

        $booking->update([
            'status' => $request->status
        ]);

        return redirect()->route('admin.booking')->with('success', 'Status booking berhasil diperbarui');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.booking')->with('success', 'Booking berhasil dihapus');
    }

    public function getAvailableSlots(BasketCourt $court)
    {
        // Ambil semua jadwal yang tersedia untuk lapangan ini
        $slots = Schedule::where('court_id', $court->id)
            ->where('status', 'available')
            ->orderBy('start_time')
            ->get()
            ->map(function($slot) {
                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time->format('H:i'),
                    'end_time' => $slot->end_time->format('H:i'),
                    'formattedTime' => $slot->start_time->format('H:i') . ' - ' . $slot->end_time->format('H:i')
                ];
            })
            ->unique('formattedTime') // Ambil jadwal unik saja
            ->values(); // Reset index array

        return response()->json($slots);
    }

    public function storeMember(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'court_id' => 'required|exists:basket_courts,id',
            'selected_time' => 'required',
            'recurring_day' => 'required|integer|between:0,6',
            'recurring_until' => 'required|date|after:today'
        ]);

        try {
            DB::beginTransaction();

            // Validasi user adalah member
            $user = User::findOrFail($request->user_id);
            if (!$user->is_member) {
                throw new \Exception('User bukan member');
            }

            // Parse selected time
            $selectedTime = json_decode($request->selected_time, true);
            if (!$selectedTime) {
                throw new \Exception('Format waktu tidak valid');
            }

            $startTime = $selectedTime['start_time'];
            $endTime = $selectedTime['end_time'];

            // Set tanggal mulai ke hari yang dipilih
            $currentDate = now();
            while ($currentDate->dayOfWeek != $request->recurring_day) {
                $currentDate->addDay();
            }
            
            $untilDate = Carbon::parse($request->recurring_until);
            $bookingsCreated = 0;
            
            while ($currentDate <= $untilDate) {
                $scheduleDate = $currentDate->format('Y-m-d');
                $fullStartTime = $scheduleDate . ' ' . $startTime;
                $fullEndTime = $scheduleDate . ' ' . $endTime;

                // Cek apakah jadwal sudah ada
                $existingSchedule = Schedule::where('court_id', $request->court_id)
                    ->whereDate('schedule_date', $scheduleDate)
                    ->whereTime('start_time', $startTime)
                    ->where('status', 'available')
                    ->first();

                if ($existingSchedule) {
                    // Update status jadwal yang ada
                    $existingSchedule->update([
                        'status' => 'booked',
                        'start_time' => $fullStartTime,
                        'end_time' => $fullEndTime
                    ]);
                    $schedule = $existingSchedule;
                    
                    \Log::info('Updating existing schedule:', [
                        'id' => $existingSchedule->id,
                        'date' => $scheduleDate,
                        'start_time' => $startTime,
                        'status' => 'booked'
                    ]);
                } else {
                    // Buat jadwal baru
                    $schedule = Schedule::create([
                        'court_id' => $request->court_id,
                        'schedule_date' => $scheduleDate,
                        'start_time' => $fullStartTime,
                        'end_time' => $fullEndTime,
                        'status' => 'booked'
                    ]);
                    
                    \Log::info('Creating new schedule:', [
                        'id' => $schedule->id,
                        'date' => $scheduleDate,
                        'start_time' => $startTime
                    ]);
                }

                // Hitung durasi dalam jam
                $duration = Carbon::parse($fullStartTime)->diffInHours(Carbon::parse($fullEndTime));

                // Generate unique booking code
                $bookingCode = 'MB' . time() . rand(100000, 999999) . $bookingsCreated;

                // Buat booking
                Booking::create([
                    'booking_code' => $bookingCode,
                    'user_id' => $request->user_id,
                    'court_id' => $request->court_id,
                    'schedule_id' => $schedule->id,
                    'duration' => $duration,
                    'total_price' => 0,
                    'status' => 'completed',
                    'is_member_booking' => true,
                    'recurring_day' => (string) $request->recurring_day,
                    'recurring_start_time' => $startTime,
                    'recurring_end_time' => $endTime,
                    'recurring_until' => $request->recurring_until
                ]);

                $bookingsCreated++;
                $currentDate->addDays(7);
            }

            if ($bookingsCreated === 0) {
                throw new \Exception('Tidak ada jadwal yang berhasil dibuat');
            }

            DB::commit();
            return redirect()->route('admin.booking')
                ->with('success', "Berhasil membuat $bookingsCreated booking member");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating member booking: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
