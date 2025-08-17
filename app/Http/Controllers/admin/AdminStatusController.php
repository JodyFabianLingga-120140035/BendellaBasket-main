<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Schedule;

class AdminStatusController extends Controller
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

    public function index()
    {
        $data = [
            'title' => 'Status Pembayaran',
            'histories' => PaymentStatusHistory::with(['booking.user', 'paymentMethod'])
                ->latest('payment_date')
                ->get()
        ];
        return view('admin.crud.status_history', $data);
    }

    public function update(Request $request, PaymentStatusHistory $status)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'payment_status' => 'required|in:pending,confirmed,failed'
            ]);

            // Update status pembayaran
            $status->update([
                'payment_status' => $request->payment_status,
                'payment_type' => $request->payment_status === 'confirmed' && $status->payment_type === 'down_payment' 
                    ? 'full_payment' 
                    : $status->payment_type
            ]);

            // Ambil booking yang terkait
            $booking = $status->booking;

            if ($request->payment_status === 'confirmed') {
                // Jika status confirmed
                $booking->update([
                    'status' => 'completed'
                ]);
            } elseif ($request->payment_status === 'failed') {
                // Jika status failed
                $booking->update([
                    'status' => 'cancelled'
                ]);

                // Hitung waktu mulai dan selesai berdasarkan durasi
                $startTime = Carbon::parse($booking->schedule->start_time);
                $endTime = $startTime->copy()->addHours($booking->duration);
                
                // Update semua jadwal dalam rentang waktu booking menjadi available
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

            DB::commit();
            return redirect()->route('admin.status')->with('success', 'Status pembayaran berhasil diperbarui');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating payment status:', [
                'status_id' => $status->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(PaymentStatusHistory $status)
    {
        if ($status->payment_proof) {
            Storage::disk('public')->delete($status->payment_proof);
        }
        
        $status->delete();
        return redirect()->route('admin.status')->with('success', 'Riwayat pembayaran berhasil dihapus');
    }
}
