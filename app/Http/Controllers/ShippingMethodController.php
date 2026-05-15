<?php

namespace App\Http\Controllers;

use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    public function index(Request $request)
    {
        $statuses = [
            'available',
            'unavailable',  // fix: was 'not available' (typo + spasi)
        ];

        $shippingMethods = ShippingMethod::query()
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->get();

        return view(
            'admin.management.shipping-methods.index',
            compact('shippingMethods', 'statuses')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider'           => 'nullable|string|max:255',  // fix: nullable, blade tidak ada required
            'courier_name'       => 'required|string|max:255',
            'courier_code'       => 'required|string|max:255',
            'service_name'       => 'required|string|max:255',
            'service_code'       => 'required|string|max:255',
            'description'        => 'nullable|string',
            'estimated_delivery' => 'nullable|string|max:255',
            'additional_fee'     => 'nullable|integer|min:0',  // integer sesuai migration bigInteger
            'status'             => 'required|in:available,unavailable',  // fix: was 'not_available'
        ]);

        ShippingMethod::create($validated);

        return redirect()->back()->with('success', 'Shipping method berhasil ditambahkan.');
    }

    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $validated = $request->validate([
            // fix: validasi lama pakai 'name' & 'shipping_cost' — field tidak ada di migration/blade
            'provider'           => 'nullable|string|max:255',
            'courier_name'       => 'required|string|max:255',
            'courier_code'       => 'required|string|max:255',
            'service_name'       => 'required|string|max:255',
            'service_code'       => 'required|string|max:255',
            'description'        => 'nullable|string',
            'estimated_delivery' => 'nullable|string|max:255',
            'additional_fee'     => 'nullable|integer|min:0',
            'status'             => 'required|in:available,unavailable',  // ada di form edit
        ]);

        $shippingMethod->update($validated);

        return redirect()->back()->with('success', 'Shipping method berhasil diperbarui.');
    }

    public function destroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();

        return redirect()->back()->with('success', 'Shipping method berhasil dihapus.');
    }

    /**
     * Toggle status available <-> unavailable via PATCH
     * Dipanggil oleh Alpine.js fetch — return JSON
     */
    public function toggleStatus(ShippingMethod $shippingMethod)
    {
        $shippingMethod->status = $shippingMethod->status === 'available'
            ? 'unavailable'
            : 'available';

        $shippingMethod->save();

        return response()->json([
            'success' => true,
            'status'  => $shippingMethod->status,
        ]);
    }
}
