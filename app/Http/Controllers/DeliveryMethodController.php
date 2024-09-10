<?php

namespace App\Http\Controllers;

use App\Models\DeliveryMethod;
use Illuminate\Http\Request;

class DeliveryMethodController extends Controller
{
    public function index(Request $request)
    {
        $statuses = ['available', 'not available'];

        $deliveryMethods = DeliveryMethod::query()
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        return view('admin.delivery', compact('deliveryMethods', 'statuses'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'shipping_cost' => 'required|numeric',
            'status' => 'required|in:available,not_available',
        ]);


        DeliveryMethod::create($validatedData);

        return redirect()->back()->with('success', 'Payment method created successfully.');
    }

    public function destroy(DeliveryMethod $deliveryMethod)
    {
        $deliveryMethod->delete();
        return redirect()->back()->with('success', 'Delivery method deleted successfully.');
    }
}
