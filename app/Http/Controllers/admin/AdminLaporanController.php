<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\BasketCourt;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class AdminLaporanController extends Controller
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
        try {
            $query = Booking::with([
                'user', 
                'court', 
                'schedule', 
                'paymentHistory' => function($q) {
                    $q->latest();
                },
                'paymentHistory.paymentMethod'
            ])->latest();

            // Filter berdasarkan lapangan
            if ($request->filled('court_id')) {
                $query->where('court_id', $request->court_id);
            }

            // Filter berdasarkan bulan
            if ($request->filled('month')) {
                $query->whereHas('schedule', function($q) use ($request) {
                    $q->whereMonth('schedule_date', $request->month);
                });
            }

            // Filter berdasarkan tahun
            if ($request->filled('year')) {
                $query->whereHas('schedule', function($q) use ($request) {
                    $q->whereYear('schedule_date', $request->year);
                });
            }

            // Filter berdasarkan status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $bookings = $query->paginate(10);

            // Debug data dengan pengecekan null
            foreach ($bookings as $booking) {
                Log::info('Booking data:', [
                    'id' => $booking->id,
                    'duration' => $booking->duration ?? 'null',
                    'total_price' => $booking->total_price ?? 'null',
                    'schedule' => $booking->schedule ? [
                        'date' => $booking->schedule->schedule_date ?? 'null',
                        'start_time' => $booking->schedule->start_time ?? 'null',
                        'end_time' => $booking->schedule->end_time ?? 'null'
                    ] : 'null',
                    'payment_history' => $booking->paymentHistory->isEmpty() ? 'empty' : $booking->paymentHistory->toArray()
                ]);
            }

            $data = [
                'title' => 'Laporan',
                'bookings' => $bookings,
                'courts' => BasketCourt::all()
            ];

            return view('admin.crud.laporan', $data);

        } catch (\Exception $e) {
            Log::error('Error in laporan index:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat memuat laporan');
        }
    }

    public function export(Request $request)
    {
        try {
            $query = Booking::with(['user', 'court', 'schedule', 'paymentHistory.paymentMethod'])
                           ->latest();

            // Aplikasikan filter yang sama seperti di index
            if ($request->filled('court_id')) {
                $query->where('court_id', $request->court_id);
            }

            if ($request->filled('month')) {
                $query->whereHas('schedule', function($q) use ($request) {
                    $q->whereMonth('schedule_date', $request->month);
                });
            }

            if ($request->filled('year')) {
                $query->whereHas('schedule', function($q) use ($request) {
                    $q->whereYear('schedule_date', $request->year);
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $bookings = $query->get();

            // Set paper size dan orientation
            $pdf = PDF::loadView('admin.exports.laporan-pdf', [
                'bookings' => $bookings,
                'filters' => $request->all()
            ])->setPaper('a4', 'landscape');

            $filename = 'laporan-booking-' . Carbon::now()->format('Y-m-d') . '.pdf';
            
            // Tambahkan header untuk menghindari caching
            return $pdf->download($filename)->header('Cache-Control', 'no-cache, no-store, must-revalidate');

        } catch (\Exception $e) {
            Log::error('Error in laporan export:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat mengekspor laporan: ' . $e->getMessage());
        }
    }
}
