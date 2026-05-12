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

        $statuses = [

            'available',
            'not_available'
        ];

        $paymentMethods = PaymentMethod::query()

            ->when($request->category, function ($query, $category) {

                return $query->where(
                    'category',
                    $category
                );
            })

            ->when($request->status, function ($query, $status) {

                return $query->where(
                    'status',
                    $status
                );
            })

            ->get();

        return view(
            'admin.management.payment_methods.index',
            compact(
                'paymentMethods',
                'categories',
                'statuses'
            )
        );
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view(
            'admin.management.payment_methods.create'
        );
    }

    /**
     * Store payment method
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Informasi Payment
            |--------------------------------------------------------------------------
            */

            'name' => 'required|string|max:255',

            /*
            |--------------------------------------------------------------------------
            | Kode unik payment
            |--------------------------------------------------------------------------
            */

            'code' => 'required|string|max:255|unique:payment_methods,code',

            /*
            |--------------------------------------------------------------------------
            | Kategori payment
            |--------------------------------------------------------------------------
            */

            'category' => 'nullable|string|max:255',

            /*
            |--------------------------------------------------------------------------
            | Provider
            |--------------------------------------------------------------------------
            */

            'provider' => 'nullable|string|max:255',

            /*
            |--------------------------------------------------------------------------
            | Informasi rekening
            |--------------------------------------------------------------------------
            */

            'account_number' => 'nullable|string|max:50',

            'account_name' => 'nullable|string|max:255',

            /*
            |--------------------------------------------------------------------------
            | Fee Payment Gateway
            |--------------------------------------------------------------------------
            */

            'fee_type' => 'nullable|in:fixed,percent',

            'fee_value' => 'nullable|numeric|min:0',

            'fee_tax_type' => 'nullable|in:before_tax,after_tax',

            /*
            |--------------------------------------------------------------------------
            | Status payment
            |--------------------------------------------------------------------------
            */

            'status' => 'required|in:available,not_available',

            /*
            |--------------------------------------------------------------------------
            | Logo payment method
            |--------------------------------------------------------------------------
            */

            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload gambar payment method
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image_url')) {

            $imagePath = $request->file('image_url')
                ->store(
                    'payment_images',
                    'public'
                );

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

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        return view(
            'admin.management.payment_methods.form-edit',
            compact('paymentMethod')
        );
    }

    /**
     * Update payment method
     */
    public function update(
        Request $request,
        PaymentMethod $paymentMethod
    ) {

        $validatedData = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Informasi Payment
            |--------------------------------------------------------------------------
            */

            'name' => 'required|string|max:255',

            'category' => 'required|string|max:255',

            /*
            |--------------------------------------------------------------------------
            | Informasi rekening
            |--------------------------------------------------------------------------
            */

            'account_number' => 'nullable|string|max:50',

            'account_name' => 'nullable|string|max:255',

            /*
            |--------------------------------------------------------------------------
            | Fee Payment Gateway
            |--------------------------------------------------------------------------
            */

            'fee_type' => 'nullable|in:fixed,percent',

            'fee_value' => 'nullable|numeric|min:0',

            'fee_tax_type' => 'nullable|in:before_tax,after_tax',

            /*
            |--------------------------------------------------------------------------
            | Logo payment method
            |--------------------------------------------------------------------------
            */

            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',


        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload gambar baru
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image_url')) {

            /*
            |--------------------------------------------------------------------------
            | Hapus gambar lama
            |--------------------------------------------------------------------------
            */

            if (
                $paymentMethod->image_url &&
                Storage::disk('public')->exists($paymentMethod->getRawOriginal('image_url'))
            ) {

                Storage::disk('public')
                    ->delete(
                        $paymentMethod->getRawOriginal('image_url')
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Upload gambar baru
            |--------------------------------------------------------------------------
            */

            $validatedData['image_url'] = $request
                ->file('image_url')
                ->store(
                    'payment_images',
                    'public'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Update payment method
        |--------------------------------------------------------------------------
        */

        $paymentMethod->update($validatedData);

        return redirect()
            ->back()
            ->with(
                'success',
                'Payment method berhasil diperbarui.'
            );
    }

    /**
     * Toggle status payment method
     */
    // PaymentMethodController.php
    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        $paymentMethod->status = $paymentMethod->status === 'available'
            ? 'unavailable'
            : 'available';

        $paymentMethod->save();

        return response()->json([
            'success' => true,
            'status'  => $paymentMethod->status,
        ]);
    }
    /**
     * Delete payment method
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        /*
        |--------------------------------------------------------------------------
        | Hapus logo payment
        |--------------------------------------------------------------------------
        */

        if (
            $paymentMethod->image_url &&
            Storage::disk('public')->exists($paymentMethod->getRawOriginal('image_url'))
        ) {

            Storage::disk('public')
                ->delete(
                    $paymentMethod->getRawOriginal('image_url')
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus payment method
        |--------------------------------------------------------------------------
        */

        $paymentMethod->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'Payment method berhasil dihapus.'
            );
    }
}
