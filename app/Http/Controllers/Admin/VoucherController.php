<?php

namespace App\Http\Controllers\Admin;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    /**
     * Tampilkan daftar voucher
     */
    public function index(Request $request)
    {
        $query = Voucher::query();

        // Filter by search
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter active only
        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }

        $vouchers = $query->latest()->paginate(15);

        return view('admin.management.vouchers.index', compact('vouchers'));
    }

    /**
     * Tampilkan form create
     */
    public function create()
    {
        return view('admin.management.vouchers.create');
    }

    /**
     * Simpan voucher baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'code'               => 'required|string|max:50|unique:vouchers,code',
            'description'        => 'nullable|string',
            'type'               => 'required|in:fixed,percent,free_shipping',
            'value'              => 'required|numeric|min:1',
            'maximum_discount'   => 'nullable|numeric|min:0',
            'minimum_purchase'   => 'nullable|numeric|min:0',
            'quota'              => 'nullable|integer|min:1',
            'max_usage_per_user' => 'nullable|integer|min:1',
            'is_active'          => 'boolean',
            'members_only'       => 'boolean',
            'is_stackable'       => 'boolean',
            'start_at'           => 'nullable|date_format:Y-m-d\TH:i',
            'end_at'             => 'nullable|date_format:Y-m-d\TH:i',
            'label'              => 'nullable|string|max:100',
            'badge_color'        => 'nullable|string|max:50',
        ]);

        Voucher::create(array_merge(
            $request->only([
                'name', 'code', 'description', 'type', 'value', 'maximum_discount',
                'minimum_purchase', 'quota', 'max_usage_per_user', 'is_active',
                'members_only', 'is_stackable', 'start_at', 'end_at', 'label', 'badge_color'
            ]),
            [
                'status' => 'active',
                'used_count' => 0,
            ]
        ));

        return redirect()
            ->route('admin.management.vouchers.index')
            ->with('success', 'Voucher berhasil dibuat');
    }

    /**
     * Tampilkan form edit
     */
    public function edit(Voucher $voucher)
    {
        return view('admin.management.vouchers.edit', compact('voucher'));
    }

    /**
     * Update voucher
     */
    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'code'               => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'description'        => 'nullable|string',
            'type'               => 'required|in:fixed,percent,free_shipping',
            'value'              => 'required|numeric|min:1',
            'maximum_discount'   => 'nullable|numeric|min:0',
            'minimum_purchase'   => 'nullable|numeric|min:0',
            'quota'              => 'nullable|integer|min:1',
            'max_usage_per_user' => 'nullable|integer|min:1',
            'is_active'          => 'boolean',
            'members_only'       => 'boolean',
            'is_stackable'       => 'boolean',
            'start_at'           => 'nullable|date_format:Y-m-d\TH:i',
            'end_at'             => 'nullable|date_format:Y-m-d\TH:i',
            'label'              => 'nullable|string|max:100',
            'badge_color'        => 'nullable|string|max:50',
        ]);

        $voucher->update($request->only([
            'name', 'code', 'description', 'type', 'value', 'maximum_discount',
            'minimum_purchase', 'quota', 'max_usage_per_user', 'is_active',
            'members_only', 'is_stackable', 'start_at', 'end_at', 'label', 'badge_color'
        ]));

        // Auto-refresh status
        $voucher->refreshStatus();

        return back()
            ->with('success', 'Voucher berhasil diperbarui');
    }

    /**
     * Hapus voucher
     */
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return back()
            ->with('success', 'Voucher berhasil dihapus');
    }

    /**
     * Generate kode voucher random
     */
    public function generateCode(Request $request)
    {
        $code = strtoupper(Str::random(10));

        // Ensure code doesn't exist
        while (Voucher::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(10));
        }

        return response()->json(['code' => $code]);
    }
}
