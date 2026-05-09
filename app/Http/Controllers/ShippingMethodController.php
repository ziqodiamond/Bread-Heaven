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
            'name' => 'required|string|max:255',
            'shipping_cost' => 'required|numeric',
            'status' => 'required|in:available,not_available',
        ]);


        ShippingMethod::create($validatedData);

        return redirect()->back()->with('success', 'Shipping method created successfully.');
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
