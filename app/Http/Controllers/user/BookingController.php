<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\BasketCourt;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(Request $request, BasketCourt $court)
    {
        // Generate booking code
        $bookingCode = 'BK-' . strtoupper(Str::random(8));
        
        // Buat booking baru dengan status pending
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'court_id' => $court->id,
            'status' => 'pending',
            'booking_code' => $bookingCode, // Tambahkan booking code
            'duration' => 1, // Default duration 1 jam
            'total_price' => $court->price_per_hour, // Set harga default
            'down_payment' => 0, // Default down payment
            'terms_accepted' => false, // Default terms
        ]);

        // Redirect ke halaman keranjang
        return redirect()->route('user.bookings.keranjang')
            ->with('success', 'Lapangan berhasil ditambahkan ke keranjang');
    }

    public function cart()
    {
        $pendingBookings = Booking::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->with('court')
            ->get();

        return view('user.bookings.cart', [
            'bookings' => $pendingBookings
        ]);
    }
} 