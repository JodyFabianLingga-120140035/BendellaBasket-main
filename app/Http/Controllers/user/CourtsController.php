<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BasketCourt;
use Illuminate\Support\Facades\Auth;

class CourtsController extends Controller
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
        $query = BasketCourt::query();

        // Search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('location', 'like', '%' . $request->search . '%');
        }

        // Sort
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price_per_hour', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price_per_hour', 'desc');
                    break;
            }
        }

        $courts = $query->where('is_available', true)
            ->paginate(9)
            ->appends($request->query());

        return view('user.courts.index', compact('courts'));
    }

    public function show(BasketCourt $court)
    {
        return view('user.courts.show', compact('court'));
    }
}