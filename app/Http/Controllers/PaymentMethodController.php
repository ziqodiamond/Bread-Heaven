<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = [

            'bank_transfer',
            'e_wallet',
            'qris',
            'retail_outlet',
            'credit_card',
        ];
        $statuses = ['available', 'not available'];

        $paymentMethods = PaymentMethod::query()
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        return view('admin.management.payment_methods.index', compact('paymentMethods', 'categories', 'statuses'));
    }

    public function create()
    {
        return view('admin.management.payment_methods.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([

            // Informasi payment
            'name' => 'required|string|max:255',

            // Kode unik payment
            'code' => 'required|string|max:255|unique:payment_methods,code',

            // Kategori payment
            'category' => 'nullable|string|max:255',

            // Provider
            'provider' => 'nullable|string|max:255',

            // Informasi rekening
            'account_number' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:255',

            // Fee tambahan
            'fee' => 'nullable|numeric',

            // Status payment
            'status' => 'required|in:available,not_available',

            // Logo / icon payment
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Upload gambar payment method
    |--------------------------------------------------------------------------
    */
        if ($request->hasFile('image_url')) {

            $imagePath = $request->file('image_url')
                ->store('payment_images', 'public');

            $validatedData['image_url'] = $imagePath;
        }

        /*
    |--------------------------------------------------------------------------
    | Simpan payment method
    |--------------------------------------------------------------------------
    */
        PaymentMethod::create($validatedData);

        return redirect()
            ->back()
            ->with(
                'success',
                'Payment method berhasil ditambahkan.'
            );
    }
    public function edit($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        return view('admin.management.payment_methods.form-edit', compact('paymentMethod'));
    }


    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'fee' => 'required|numeric',
            'status' => 'required|in:available,not_available',
        ]);

        // Cek apakah ada file gambar yang diunggah
        if ($request->hasFile('image_url')) {
            // Hapus gambar lama jika ada
            if ($paymentMethod->image_url) {
                Storage::delete($paymentMethod->image_url);
            }

            // Simpan gambar yang baru diunggah
            $validatedData['image_url'] = $request->file('image_url')->store('payment-images');
        }

        // Perbarui payment method dengan data yang divalidasi
        $paymentMethod->update($validatedData);

        return redirect()->back()->with('success', 'Payment method updated successfully.');
    }



    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return redirect()->back()->with('success', 'Payment method deleted successfully.');
    }
}
