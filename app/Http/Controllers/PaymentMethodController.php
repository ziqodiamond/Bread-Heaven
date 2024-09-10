<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = PaymentMethod::select('category')->distinct()->pluck('category');
        $statuses = ['available', 'not available'];

        $paymentMethods = PaymentMethod::query()
            ->when($request->category, function ($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->get();

        return view('admin.payment', compact('paymentMethods', 'categories', 'statuses'));
    }

    public function create()
    {
        return view('admin.payment');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'fee' => 'required|numeric',
            'status' => 'required|in:available,not_available',
        ]);


        PaymentMethod::create($validatedData);

        return redirect()->back()->with('success', 'Payment method created successfully.');
    }

    public function edit($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        return view('admin.payment_methods.form-edit', compact('paymentMethod'));
    }


    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'fee' => 'required|numeric',
            'status' => 'required|in:available,not_available',
        ]);


        $paymentMethod->update($validatedData);

        return redirect()->back()->with('success', 'Payment method updated successfully.');
    }


    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return redirect()->back()->with('success', 'Payment method deleted successfully.');
    }
}
