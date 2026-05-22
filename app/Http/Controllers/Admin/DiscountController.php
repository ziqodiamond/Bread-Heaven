<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscountController extends Controller
{
    /**
     * Tampilkan daftar semua diskon produk
     */
    public function index(Request $request)
    {
        $query = Product::where('sale_price', '>', 0)
            ->with('images');

        // Filter by search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by discount type
        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        // Filter active discount only
        if ($request->boolean('active_only')) {
            $query->where(function ($q) {
                $q->whereNull('discount_start_at')
                    ->orWhere('discount_start_at', '<=', now());
            })->where(function ($q) {
                $q->whereNull('discount_end_at')
                    ->orWhere('discount_end_at', '>=', now());
            });
        }

        $products = $query->latest()->paginate(15);

        return view('admin.management.discounts.index', compact('products'));
    }

    /**
     * Edit halaman diskon produk
     */
    public function edit(Product $product)
    {
        return view('admin.management.discounts.edit', compact('product'));
    }

    /**
     * Update diskon produk
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'sale_price'         => 'nullable|integer|min:0',
            'discount_type'      => 'nullable|in:percent,fixed',
            'discount_value'     => 'nullable|numeric|min:0',
            'discount_max'       => 'nullable|integer|min:0|max:100',
            'discount_label'     => 'nullable|string|max:255',
            'discount_start_at'  => 'nullable|date_format:Y-m-d\TH:i',
            'discount_end_at'    => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        // Jika tipe persen, pastikan nilai persen tidak melebihi batas maksimal jika diberikan
        if ($request->filled('discount_type') && $request->discount_type === 'percent' && $request->filled('discount_value') && $request->filled('discount_max')) {
            if ((float) $request->discount_value > (int) $request->discount_max) {
                return back()
                    ->withInput()
                    ->withErrors(['discount_value' => 'Nilai diskon persen tidak boleh melebihi maksimal yang ditentukan']);
            }
        }

        // Validasi: jika tidak ada sale_price, jadikan null
        $salePrice = $request->filled('sale_price') 
            ? $request->integer('sale_price') 
            : null;

        // Validasi: sale_price tidak boleh lebih besar dari harga normal
        if ($salePrice && $salePrice >= $product->price) {
            return back()
                ->withInput()
                ->withErrors(['sale_price' => 'Harga diskon harus lebih kecil dari harga normal']);
        }

        $product->update([
            'sale_price'        => $salePrice,
            'discount_type'     => $request->filled('discount_type') ? $request->discount_type : null,
            'discount_value'    => $request->filled('discount_value') ? $request->discount_value : null,
            'discount_label'    => $request->discount_label,
            'discount_start_at' => $request->discount_start_at,
            'discount_end_at'   => $request->discount_end_at,
        ]);

        return redirect()
            ->route('admin.management.discounts.index')
            ->with('success', 'Diskon produk berhasil diperbarui');
    }

    /**
     * Hapus diskon dari produk
     */
    public function destroy(Product $product)
    {
        $product->update([
            'sale_price'        => null,
            'discount_type'     => null,
            'discount_value'    => null,
            'discount_label'    => null,
            'discount_start_at' => null,
            'discount_end_at'   => null,
        ]);

        return back()
            ->with('success', 'Diskon produk berhasil dihapus');
    }

    /**
     * Bulk update diskon status
     */
    public function bulkToggle(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'uuid|exists:products,id',
            'action' => 'required|in:activate,deactivate,delete',
        ]);

        $ids = $request->product_ids;

        match ($request->action) {
            'activate' => Product::whereIn('id', $ids)->update([
                'discount_start_at' => now(),
            ]),
            'deactivate' => Product::whereIn('id', $ids)->update([
                'discount_end_at' => now(),
            ]),
            'delete' => Product::whereIn('id', $ids)->update([
                'sale_price'        => null,
                'discount_type'     => null,
                'discount_value'    => null,
                'discount_label'    => null,
                'discount_start_at' => null,
                'discount_end_at'   => null,
            ]),
        };

        return response()->json([
            'success' => true,
            'message' => 'Diskon berhasil diperbarui'
        ]);
    }
}
