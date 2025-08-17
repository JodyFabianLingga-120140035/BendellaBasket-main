<?php

namespace App\Http\Controllers;

use App\Models\BasketCourt;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        $courts = BasketCourt::where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('welcome', compact('courts'));
    }
}
