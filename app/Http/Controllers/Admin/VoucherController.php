<?php

namespace App\Http\Controllers\Admin;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

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
        // Ambil referensi untuk pilihan relasi — guard jika model / table tidak tersedia
        $products = (class_exists(\App\Models\Product::class) && Schema::hasTable('products')) ? \App\Models\Product::orderBy('name')->get() : collect();

        $categories = (class_exists(\App\Models\Category::class) && Schema::hasTable('categories')) ? \App\Models\Category::active()->ordered()->get() : collect();

        $shippingMethods = (class_exists(\App\Models\ShippingMethod::class) && Schema::hasTable('shipping_methods')) ? \App\Models\ShippingMethod::where('status','available')->orderBy('courier_name')->get() : collect();

        $paymentMethods = (class_exists(\App\Models\PaymentMethod::class) && Schema::hasTable('payment_methods')) ? \App\Models\PaymentMethod::available()->orderBy('name')->get() : collect();

        return view('admin.management.vouchers.create', compact('products','categories','shippingMethods','paymentMethods'));
    }

    /**
     * Simpan voucher baru
     */
    public function store(Request $request)
    {
        // Validate dengan rules dinamis
        $request->validate($this->validationRules());

        $data = $request->only([
            'name', 'code', 'description', 'type', 'value', 'maximum_discount',
            'minimum_purchase', 'quota', 'max_usage_per_user', 'is_active',
            'members_only', 'is_stackable', 'start_at', 'end_at', 'label', 'badge_color'
        ]);

        // Handle image upload
        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/vouchers', $fileName);
            $data['image_path'] = 'vouchers/' . $fileName;
        }

        // Tambah default values
        $data['status'] = 'active';
        $data['used_count'] = 0;

        $voucher = Voucher::create($data);

        // Sync relasi include/exclude jika ada
        $this->syncRelationsFromRequest($voucher, $request);

        return redirect()
            ->route('admin.management.vouchers.index')
            ->with('success', 'Voucher berhasil dibuat');
    }

    /**
     * Tampilkan form edit
     */
    public function edit(Voucher $voucher)
    {
        // Ambil referensi untuk pilihan relasi — guard jika model / table tidak tersedia
        $products = (class_exists(\App\Models\Product::class) && Schema::hasTable('products')) ? \App\Models\Product::orderBy('name')->get() : collect();

        $categories = (class_exists(\App\Models\Category::class) && Schema::hasTable('categories')) ? \App\Models\Category::active()->ordered()->get() : collect();

        $shippingMethods = (class_exists(\App\Models\ShippingMethod::class) && Schema::hasTable('shipping_methods')) ? \App\Models\ShippingMethod::where('status','available')->orderBy('courier_name')->get() : collect();

        $paymentMethods = (class_exists(\App\Models\PaymentMethod::class) && Schema::hasTable('payment_methods')) ? \App\Models\PaymentMethod::available()->orderBy('name')->get() : collect();

        // Ambil relasi existing untuk prefill
        $voucher->load('products', 'categories', 'shippingMethods', 'paymentMethods');

        return view('admin.management.vouchers.edit', compact('voucher','products','categories','shippingMethods','paymentMethods'));
    }

    /**
     * Update voucher
     */
    public function update(Request $request, Voucher $voucher)
    {
        // Gunakan rule dinamis (update) — pass voucher untuk unique rule
        $request->validate($this->validationRules($voucher));

        $data = $request->only([
            'name', 'code', 'description', 'type', 'value', 'maximum_discount',
            'minimum_purchase', 'quota', 'max_usage_per_user', 'is_active',
            'members_only', 'is_stackable', 'start_at', 'end_at', 'label', 'badge_color'
        ]);

        // Handle image upload
        if ($request->hasFile('image_path')) {
            // Delete old image if exists
            if ($voucher->image_path && \Storage::disk('public')->exists($voucher->image_path)) {
                \Storage::disk('public')->delete($voucher->image_path);
            }

            // Upload new image
            $file = $request->file('image_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/vouchers', $fileName);
            $data['image_path'] = 'vouchers/' . $fileName;
        }

        $voucher->update($data);

        // Sync relasi include/exclude jika ada
        $this->syncRelationsFromRequest($voucher, $request);

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

    /**
     * Helper: buat validation rules dinamis untuk store/update
     */
    protected function validationRules(?Voucher $voucher = null): array
    {
        // Base rules
        $rules = [
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
            'allow_on_flash_sale' => 'boolean',
            'allow_on_discount'  => 'boolean',
            'start_at'           => 'nullable|date_format:Y-m-d\\TH:i',
            'end_at'             => 'nullable|date_format:Y-m-d\\TH:i',
            'label'              => 'nullable|string|max:100',
            'badge_color'        => 'nullable|string|max:50',
            'image_path'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            // New comma-separated string format
            'category_ids'       => 'nullable|string',
            'shipping_method_ids' => 'nullable|string',
            'payment_method_ids' => 'nullable|string',

            // Arrays defaults (legacy support)
            'included_products' => 'nullable|array',
            'included_products.*' => 'uuid',
            'excluded_products' => 'nullable|array',
            'excluded_products.*' => 'uuid',

            'included_shipping_methods' => 'nullable|array',
            'included_shipping_methods.*' => 'integer',
            'excluded_shipping_methods' => 'nullable|array',
            'excluded_shipping_methods.*' => 'integer',

            'included_payment_methods' => 'nullable|array',
            'included_payment_methods.*' => 'integer',
            'excluded_payment_methods' => 'nullable|array',
            'excluded_payment_methods.*' => 'integer',
        ];

        // Unique rule adjustment untuk update
        if ($voucher) {
            $rules['code'] = 'required|string|max:50|unique:vouchers,code,' . $voucher->id;
        }

        // Conditional exists rules only if tables exist
        if (class_exists(\App\Models\Product::class) && Schema::hasTable('products')) {
            $rules['included_products.*'] = 'uuid|exists:products,id';
            $rules['excluded_products.*'] = 'uuid|exists:products,id';
        }

        if (class_exists(\App\Models\Category::class) && Schema::hasTable('categories')) {
            $rules['included_categories'] = 'nullable|array';
            $rules['included_categories.*'] = 'integer|exists:categories,id';
            $rules['excluded_categories'] = 'nullable|array';
            $rules['excluded_categories.*'] = 'integer|exists:categories,id';
        }

        if (class_exists(\App\Models\ShippingMethod::class) && Schema::hasTable('shipping_methods')) {
            $rules['included_shipping_methods.*'] = 'integer|exists:shipping_methods,id';
            $rules['excluded_shipping_methods.*'] = 'integer|exists:shipping_methods,id';
        }

        if (class_exists(\App\Models\PaymentMethod::class) && Schema::hasTable('payment_methods')) {
            $rules['included_payment_methods.*'] = 'integer|exists:payment_methods,id';
            $rules['excluded_payment_methods.*'] = 'integer|exists:payment_methods,id';
        }

        return $rules;
    }

    /**
     * Helper: sync relations include / exclude from request
     */
    protected function syncRelationsFromRequest(Voucher $voucher, Request $request): void
    {
        // Categories - new format with comma-separated IDs
        if ($request->filled('category_ids')) {
            $categoryIds = array_filter(explode(',', $request->input('category_ids')));
            $sync = [];
            foreach ($categoryIds as $id) {
                $sync[$id] = ['is_excluded' => false];
            }
            $voucher->categories()->sync($sync);
        } elseif ($request->has('category_ids')) {
            // Empty string means clear all
            $voucher->categories()->sync([]);
        }

        // Shipping Methods - new format with comma-separated IDs
        if ($request->filled('shipping_method_ids')) {
            $methodIds = array_filter(explode(',', $request->input('shipping_method_ids')));
            $sync = [];
            foreach ($methodIds as $id) {
                $sync[$id] = ['is_excluded' => false];
            }
            $voucher->shippingMethods()->sync($sync);
        } elseif ($request->has('shipping_method_ids')) {
            // Empty string means clear all
            $voucher->shippingMethods()->sync([]);
        }

        // Payment Methods - new format with comma-separated IDs
        if ($request->filled('payment_method_ids')) {
            $methodIds = array_filter(explode(',', $request->input('payment_method_ids')));
            $sync = [];
            foreach ($methodIds as $id) {
                $sync[$id] = ['is_excluded' => false];
            }
            $voucher->paymentMethods()->sync($sync);
        } elseif ($request->has('payment_method_ids')) {
            // Empty string means clear all
            $voucher->paymentMethods()->sync([]);
        }

        // Legacy support for old include/exclude format
        // Products
        if ($request->has('included_products') || $request->has('excluded_products')) {
            $included = $request->input('included_products', []);
            $excluded = $request->input('excluded_products', []);

            $sync = [];
            foreach ($included as $id) {
                $sync[$id] = ['is_excluded' => false];
            }
            foreach ($excluded as $id) {
                $sync[$id] = ['is_excluded' => true];
            }

            $voucher->products()->sync($sync);
        }

        // Old Categories format (if still used)
        if ($request->has('included_categories') || $request->has('excluded_categories')) {
            $included = $request->input('included_categories', []);
            $excluded = $request->input('excluded_categories', []);
            $sync = [];
            foreach ($included as $id) {
                $sync[$id] = ['is_excluded' => false];
            }
            foreach ($excluded as $id) {
                $sync[$id] = ['is_excluded' => true];
            }
            $voucher->categories()->sync($sync);
        }

        // Old Shipping Methods format
        if ($request->has('included_shipping_methods') || $request->has('excluded_shipping_methods')) {
            $included = $request->input('included_shipping_methods', []);
            $excluded = $request->input('excluded_shipping_methods', []);
            $sync = [];
            foreach ($included as $id) {
                $sync[$id] = ['is_excluded' => false];
            }
            foreach ($excluded as $id) {
                $sync[$id] = ['is_excluded' => true];
            }
            $voucher->shippingMethods()->sync($sync);
        }

        // Old Payment Methods format
        if ($request->has('included_payment_methods') || $request->has('excluded_payment_methods')) {
            $included = $request->input('included_payment_methods', []);
            $excluded = $request->input('excluded_payment_methods', []);
            $sync = [];
            foreach ($included as $id) {
                $sync[$id] = ['is_excluded' => false];
            }
            foreach ($excluded as $id) {
                $sync[$id] = ['is_excluded' => true];
            }
            $voucher->paymentMethods()->sync($sync);
        }
    }
}

