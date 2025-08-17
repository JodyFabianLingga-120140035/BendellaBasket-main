<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\BasketCourt;
use App\Models\Booking;
use App\Models\PaymentStatusHistory;
use Carbon\Carbon;

class AdminController extends Controller
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
        // Ambil 5 aktivitas terbaru
        $recentActivities = PaymentStatusHistory::with(['booking.user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'user_name' => $activity->booking->user->name,
                    'payment_status' => $activity->payment_status,
                    'amount' => $activity->amount_paid,
                    'created_at' => $activity->created_at,
                    'time_ago' => Carbon::parse($activity->created_at)->diffForHumans()
                ];
            });

        $data = [
            'title' => 'Admin Dashboard',
            'users' => User::count(),
            'basket_courts' => BasketCourt::count(),
            'bookings' => Booking::whereIn('status', ['confirmed', 'completed'])->count(),
            'recentActivities' => $recentActivities
        ];
        
        return view('admin.layouts.home', $data);
    }
}
