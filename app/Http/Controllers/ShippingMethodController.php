<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    public function index(Request $request)
    {
        $statuses = ['available', 'not available'];

        $shippingMethods = ShippingMethod::query()
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        return view('admin.management.shipping-methods.index', compact('shippingMethods', 'statuses'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([

            /*
        |--------------------------------------------------------------------------
        | Provider Shipping
        |--------------------------------------------------------------------------
        */

            'provider' => 'required|string|max:255',

            /*
        |--------------------------------------------------------------------------
        | Informasi Courier
        |--------------------------------------------------------------------------
        */

            'courier_name' => 'required|string|max:255',

            'courier_code' => 'required|string|max:255',

            /*
        |--------------------------------------------------------------------------
        | Informasi Service
        |--------------------------------------------------------------------------
        */

            'service_name' => 'required|string|max:255',

            'service_code' => 'required|string|max:255',

            /*
        |--------------------------------------------------------------------------
        | Deskripsi
        |--------------------------------------------------------------------------
        */

            'description' => 'nullable|string',

            /*
        |--------------------------------------------------------------------------
        | Estimasi Pengiriman
        |--------------------------------------------------------------------------
        */

            'estimated_delivery' => 'nullable|string|max:255',

            /*
        |--------------------------------------------------------------------------
        | Fee Tambahan
        |--------------------------------------------------------------------------
        */

            'additional_fee' => 'nullable|numeric|min:0',

            /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

            'status' => 'required|in:available,not_available',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Simpan shipping method
    |--------------------------------------------------------------------------
    */
        ShippingMethod::create($validatedData);

        return redirect()
            ->back()
            ->with(
                'success',
                'Shipping method berhasil ditambahkan.'
            );
    }

    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'shipping_cost' => 'required|numeric',
            'status' => 'required|in:available,not_available',
        ]);

        // Perbarui payment method dengan data yang divalidasi
        $shippingMethod->update($validatedData);

        return redirect()->back()->with('success', 'Shipping method updated successfully.');
    }

    public function destroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();
        return redirect()->back()->with('success', 'Shipping method deleted successfully.');
    }
}
