<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BasketCourt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends Controller
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

    public function index()
    {
        $user = Auth::user();
        
        // Get all courts with their details
        $courts = BasketCourt::with(['schedules' => function($query) {
            $query->whereDate('schedule_date', '>=', now())
                  ->orderBy('schedule_date', 'asc')
                  ->orderBy('start_time', 'asc');
        }])->get();

        // Cart count
        $cartCount = Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        return view('user.index', [
            'courts' => $courts,
            'cartCount' => $cartCount
        ]);
    }
}
