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
            'unavailable',   // sync: migration enum & toggleStatus pakai 'unavailable'
        ];

        $paymentMethods = PaymentMethod::query()
            ->when($request->category, fn($q, $v) => $q->where('category', $v))
            ->when($request->status,   fn($q, $v) => $q->where('status', $v))
            ->get();

        return view(
            'admin.management.payment_methods.index',
            compact('paymentMethods', 'categories', 'statuses')
        );
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.management.payment_methods.create');
    }

    /**
     * Store payment method.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:255|unique:payment_methods,code',
            'category'       => 'required|string|max:255',  // required — form create punya required attr
            'provider'       => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'account_name'   => 'nullable|string|max:255',
            'fee_type'       => 'nullable|in:fixed,percent',
            'fee_value'      => 'nullable|numeric|min:0',
            'fee_tax_type'   => 'nullable|in:before_tax,after_tax',
            'status'         => 'required|in:available,unavailable',
            'image_url'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image_url')) {
            $validated['image_url'] = $request->file('image_url')
                ->store('payment_images', 'public');
        }

        PaymentMethod::create($validated);

        return redirect()->back()->with('success', 'Payment method berhasil ditambahkan.');
    }

    /**
     * Show edit form.
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
     * Update payment method.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:255|unique:payment_methods,code,' . $paymentMethod->id . ',id',
            'category'       => 'required|string|max:255',
            'provider'       => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'account_name'   => 'nullable|string|max:255',
            'fee_type'       => 'nullable|in:fixed,percent',
            'fee_value'      => 'nullable|numeric|min:0',
            'fee_tax_type'   => 'nullable|in:before_tax,after_tax',
            'image_url'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image_url')) {
            if (
                $paymentMethod->image_url &&
                Storage::disk('public')->exists($paymentMethod->getRawOriginal('image_url'))
            ) {
                Storage::disk('public')->delete($paymentMethod->getRawOriginal('image_url'));
            }

            $validated['image_url'] = $request->file('image_url')
                ->store('payment_images', 'public');
        }

        $paymentMethod->update($validated);

        return redirect()->back()->with('success', 'Payment method berhasil diperbarui.');
    }

    /**
     * Toggle status payment method (PATCH, dipanggil via Alpine fetch).
     */
    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        $paymentMethod->status = $paymentMethod->status === 'available'
            ? 'unavailable'   // sync: enum migration & statuses di blade
            : 'available';

        $paymentMethod->save();

        return response()->json([
            'success' => true,
            'status'  => $paymentMethod->status,
        ]);
    }

    /**
     * Delete payment method.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        if (
            $paymentMethod->image_url &&
            Storage::disk('public')->exists($paymentMethod->getRawOriginal('image_url'))
        ) {
            Storage::disk('public')->delete($paymentMethod->getRawOriginal('image_url'));
        }

        $paymentMethod->delete();

        return redirect()->back()->with('success', 'Payment method berhasil dihapus.');
    }
}
