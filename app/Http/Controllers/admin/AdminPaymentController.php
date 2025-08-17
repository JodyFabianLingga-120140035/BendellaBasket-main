<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Storage;

class AdminPaymentController extends Controller
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
            'title' => 'Metode Pembayaran',
            'payments' => PaymentMethod::latest()->get()
        ];
        return view('admin.crud.payment', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'qris_img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'nullable'
        ]);

        $data = [
            'name' => $request->name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'is_active' => $request->has('is_active') ? 1 : 0
        ];

        if ($request->hasFile('qris_img')) {
            $qrisImage = $request->file('qris_img');
            $filename = time() . '.' . $qrisImage->getClientOriginalExtension();
            $path = $qrisImage->storeAs('qris-images', $filename, 'public');
            $data['qris_img'] = $path;
        }

        PaymentMethod::create($data);

        return redirect()->route('admin.payment')->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    public function update(Request $request, PaymentMethod $payment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'qris_img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'nullable'
        ]);

        $data = [
            'name' => $request->name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'is_active' => $request->has('is_active') ? 1 : 0
        ];

        if ($request->hasFile('qris_img')) {
            if ($payment->qris_img) {
                Storage::disk('public')->delete($payment->qris_img);
            }

            $qrisImage = $request->file('qris_img');
            $filename = time() . '.' . $qrisImage->getClientOriginalExtension();
            $path = $qrisImage->storeAs('qris-images', $filename, 'public');
            $data['qris_img'] = $path;
        }

        $payment->update($data);

        return redirect()->route('admin.payment')->with('success', 'Metode pembayaran berhasil diperbarui');
    }

    public function destroy(PaymentMethod $payment)
    {
        if ($payment->qris_img) {
            Storage::disk('public')->delete($payment->qris_img);
        }
        
        $payment->delete();
        return redirect()->route('admin.payment')->with('success', 'Metode pembayaran berhasil dihapus');
    }
}
