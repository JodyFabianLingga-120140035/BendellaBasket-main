<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\BasketCourt;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;

class AdminScheduleController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            abort(404);
        }

        if (Auth::user()->role_id != 1) {
            abort(404); 
        }
    }

    public function index(Request $request)
    {
        $weekOffset = (int) $request->query('week', 0);
        $currentDate = now()->addWeeks($weekOffset);
        $startDate = $currentDate->copy()->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        $courts = BasketCourt::where('is_available', true)->get();
        $selectedCourtId = $request->query('court_id', $courts->first()->id);

        $data = [
            'title' => 'Jadwal',
            'schedules' => Schedule::with(['court', 'booking.user'])
                ->whereDate('schedule_date', '>=', $startDate)
                ->whereDate('schedule_date', '<=', $endDate)
                ->get()
                ->map(function ($schedule) {
                    $bookerName = null;
                    if ($schedule->status === 'booked' && $schedule->booking && $schedule->booking->user) {
                        $bookerName = $schedule->booking->user->name;
                    }
                    return [
                        'id' => $schedule->id,
                        'court_id' => $schedule->court_id,
                        'schedule_date' => date('Y-m-d', strtotime($schedule->schedule_date)),
                        'start_time' => date('H:i', strtotime($schedule->start_time)),
                        'end_time' => date('H:i', strtotime($schedule->end_time)),
                        'status' => $schedule->status,
                        'notes' => $schedule->notes,
                        'booker_name' => $bookerName,
                        'court' => [
                            'id' => $schedule->court->id,
                            'name' => $schedule->court->name
                        ]
                    ];
                }),
            'courts' => $courts,
            'currentDate' => $currentDate,
            'selectedCourtId' => $selectedCourtId
        ];
        
        // Debug data
        \Log::info('Schedules data:', ['schedules' => $data['schedules']]);
        
        return view('admin.crud.schedules', $data);
    }

    public function store(Request $request)
    {
        \Log::info('Request data:', $request->all());
        
        $request->validate([
            'court_id' => 'required|exists:basket_courts,id',
            'schedule_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i|after_or_equal:06:00|before:23:00',
            'end_time' => 'required|date_format:H:i|after:start_time|before_or_equal:23:00',
            'notes' => 'nullable|string|max:255',
            'repeat_duration' => 'required_if:is_recurring,1|in:1,2,3,6,12'
        ]);

        // Fungsi untuk membuat satu jadwal
        $createSchedule = function($date) use ($request) {
            return Schedule::create([
                'court_id' => $request->court_id,
                'schedule_date' => $date,
                'start_time' => $date . ' ' . $request->start_time,
                'end_time' => $date . ' ' . $request->end_time,
                'status' => 'available',
                'notes' => $request->notes
            ]);
        };

        // Buat jadwal pertama
        $createSchedule($request->schedule_date);

        // Jika is_recurring dicentang
        if ($request->has('is_recurring')) {
            try {
                $baseDate = Carbon::parse($request->schedule_date);
                $monthsToAdd = (int) $request->repeat_duration;
                $endDate = Carbon::parse($request->schedule_date)->addMonths($monthsToAdd);
                
                $currentDate = $baseDate->copy()->addWeek(); // Mulai dari minggu depan
                
                while ($currentDate->lte($endDate)) {
                    $createSchedule($currentDate->format('Y-m-d'));
                    $currentDate->addWeek();
                }
            } catch (\Exception $e) {
                \Log::error('Error creating recurring schedules: ' . $e->getMessage());
                return redirect()->route('admin.schedule')
                               ->with('error', 'Terjadi kesalahan saat membuat jadwal berulang');
            }
        }

        return redirect()->route('admin.schedule')
                        ->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function update(Request $request, Schedule $schedule)
    {
        \Log::info('Update request data:', $request->all());
        
        $request->validate([
            'court_id' => 'required|exists:basket_courts,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required|date_format:H:i|after_or_equal:06:00|before:23:00',
            'end_time' => 'required|date_format:H:i|after:start_time|before_or_equal:23:00',
            'status' => 'required|in:available,booked,holiday',
            'notes' => 'nullable|string|max:255'
        ]);

        try {
            $updateData = [
                'court_id' => $request->court_id,
                'schedule_date' => $request->schedule_date,
                'start_time' => $request->schedule_date . ' ' . $request->start_time,
                'end_time' => $request->schedule_date . ' ' . $request->end_time,
                'status' => $request->status,
                'notes' => $request->notes
            ];
            
            $schedule->update($updateData);

            \Log::info('Schedule updated:', [
                'id' => $schedule->id,
                'notes' => $request->notes,
                'updated_data' => $schedule->fresh()->toArray()
            ]);

            return redirect()->route('admin.schedule')->with('success', 'Jadwal berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Error updating schedule:', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return redirect()->route('admin.schedule')->with('error', 'Gagal memperbarui jadwal');
        }
    }

    public function destroy(Schedule $schedule)
    {
        if ($schedule->status === 'booked') {
            return redirect()->route('admin.schedule')->with('error', 'Tidak dapat menghapus jadwal yang sudah dibooking');
        }

        $schedule->delete();
        return redirect()->route('admin.schedule')->with('success', 'Jadwal berhasil dihapus');
    }

    public function destroyRecurring(Schedule $schedule, Request $request)
    {
        try {
            // Validasi durasi
            $request->validate([
                'duration' => 'required|in:1,2,3,6,12'
            ]);

            // Validasi status jadwal
            if ($schedule->status === 'booked') {
                throw new \Exception('Tidak dapat menghapus jadwal yang sudah dibooking');
            }

            DB::beginTransaction();

            $duration = (int) $request->duration;
            
            // Ambil tanggal dan waktu dari jadwal yang dipilih
            $baseDate = Carbon::parse($schedule->schedule_date);
            $endDate = $baseDate->copy()->addMonths($duration);
            $dayOfWeek = $baseDate->dayOfWeek;
            $startTime = date('H:i', strtotime($schedule->start_time));
            $endTime = date('H:i', strtotime($schedule->end_time));
            
            // Ambil semua jadwal yang akan dihapus
            $schedules = Schedule::where('court_id', $schedule->court_id)
                ->where(function ($query) use ($baseDate, $endDate, $dayOfWeek, $startTime, $endTime) {
                    $query->whereRaw('DAYOFWEEK(schedule_date) = ?', [$dayOfWeek + 1])
                        ->whereDate('schedule_date', '>=', $baseDate)
                        ->whereDate('schedule_date', '<=', $endDate)
                        ->whereTime('start_time', $startTime)
                        ->whereTime('end_time', $endTime);
                })
                ->where('status', '!=', 'booked')
                ->get();

            if ($schedules->isEmpty()) {
                throw new \Exception('Tidak ada jadwal yang dapat dihapus');
            }

            // Hitung jumlah jadwal yang akan dihapus
            $deletedCount = $schedules->count();

            // Hapus jadwal satu per satu
            foreach ($schedules as $scheduleToDelete) {
                $scheduleToDelete->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} jadwal untuk {$duration} bulan ke depan"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting recurring schedules:', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
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

            // Ambil slot pertama dan terakhir
            $firstSlot = $request->slots[0];
            $lastSlot = end($request->slots);

            // Update booking dengan jadwal baru
            $schedule = Schedule::where('court_id', $booking->court_id)
                ->where('schedule_date', $booking->schedule->schedule_date)
                ->where('start_time', $firstSlot['time'])
                ->first();

            if (!$schedule) {
                throw new \Exception('Jadwal tidak tersedia');
            }

            // Validasi semua slot yang dipilih tersedia
            foreach ($request->slots as $slot) {
                $slotSchedule = Schedule::where('court_id', $booking->court_id)
                    ->where('schedule_date', $booking->schedule->schedule_date)
                    ->where('start_time', $slot['time'])
                    ->where('status', 'available')
                    ->first();

                if (!$slotSchedule) {
                    throw new \Exception('Salah satu jadwal yang dipilih tidak tersedia');
                }
            }

            // Update semua slot yang dipilih
            foreach ($request->slots as $slot) {
                Schedule::where('court_id', $booking->court_id)
                    ->where('schedule_date', $booking->schedule->schedule_date)
                    ->where('start_time', $slot['time'])
                    ->update(['status' => 'booked']);
            }

            // Update booking
            $booking->update([
                'schedule_id' => $schedule->id,
                'duration' => $request->duration,
                'total_price' => $request->duration * $booking->court->price_per_hour
            ]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
