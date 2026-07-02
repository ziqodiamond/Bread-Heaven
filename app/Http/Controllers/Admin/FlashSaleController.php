<?php

namespace App\Http\Controllers\Admin;

use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FlashSaleController extends Controller
{
    /**
     * Tampilkan daftar flash sale
     */
    public function index(Request $request)
    {
        $query = FlashSale::query();

        // Filter by search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $flashSales = $query->with('items')->latest()->paginate(15);

        return view('admin.management.flash_sales.index', compact('flashSales'));
    }

    /**
     * Tampilkan form create
     */
    public function create()
    {
        $products = Product::available()->inStock()->get();
        return view('admin.management.flash_sales.create', compact('products'));
    }

    /**
     * Simpan flash sale baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'banner'            => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'thumbnail'         => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'label'             => 'required|string|max:100',
            'badge_color'       => 'nullable|string|max:50',
            'start_at'          => 'required|date_format:Y-m-d H:i',
            'end_at'            => 'required|date_format:Y-m-d H:i|after:start_at',
            'is_active'         => 'boolean',
            'show_countdown'    => 'boolean',
            'show_in_homepage'  => 'boolean',
            'sort_order'        => 'nullable|integer',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'name', 'description', 'label', 'badge_color',
            'start_at', 'end_at', 'is_active', 'show_countdown',
            'show_in_homepage', 'sort_order', 'meta_title', 'meta_description'
        ]);

        // Upload banner
        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('flash_sales/banners', 'public');
        }

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('flash_sales/thumbnails', 'public');
        }

        $flashSale = FlashSale::create(array_merge(
            $data,
            [
                'slug' => str()->slug($request->name),
                'status' => 'scheduled',
            ]
        ));

        return redirect()
            ->route('admin.management.flash_sales.edit', $flashSale)
            ->with('success', 'Flash sale berhasil dibuat');
    }

    /**
     * Tampilkan form edit
     */
    public function edit(FlashSale $flashSale)
    {
        $products = Product::available()->inStock()->get();
        $items = $flashSale->items()->with('product')->get();
        
        return view('admin.management.flash_sales.edit', compact('flashSale', 'products', 'items'));
    }

    /**
     * Update flash sale
     */
    public function update(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'banner'            => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'thumbnail'         => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'label'             => 'required|string|max:100',
            'badge_color'       => 'nullable|string|max:50',
            'start_at'          => 'required|date_format:Y-m-d H:i',
            'end_at'            => 'required|date_format:Y-m-d H:i|after:start_at',
            'is_active'         => 'boolean',
            'show_countdown'    => 'boolean',
            'show_in_homepage'  => 'boolean',
            'sort_order'        => 'nullable|integer',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
        ]);

        $data = $request->only([
            'name', 'description', 'label', 'badge_color',
            'start_at', 'end_at', 'is_active', 'show_countdown',
            'show_in_homepage', 'sort_order', 'meta_title', 'meta_description'
        ]);

        // Upload banner
        if ($request->hasFile('banner')) {
            // Hapus file lama jika ada
            if ($flashSale->banner && Storage::disk('public')->exists($flashSale->banner)) {
                Storage::disk('public')->delete($flashSale->banner);
            }
            $data['banner'] = $request->file('banner')->store('flash_sales/banners', 'public');
        }

        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            // Hapus file lama jika ada
            if ($flashSale->thumbnail && Storage::disk('public')->exists($flashSale->thumbnail)) {
                Storage::disk('public')->delete($flashSale->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('flash_sales/thumbnails', 'public');
        }

        $flashSale->update($data);

        // Auto-refresh status
        $flashSale->refreshStatus();

        return back()
            ->with('success', 'Flash sale berhasil diperbarui');
    }

    /**
     * Hapus flash sale
     */
    public function destroy(FlashSale $flashSale)
    {
        $flashSale->delete();

        return redirect()
            ->route('admin.management.flash_sales.index')
            ->with('success', 'Flash sale berhasil dihapus');
    }

    /**
     * Tambah item ke flash sale
     */
    public function addItem(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'product_id'            => 'required|uuid|exists:products,id',
            'flash_sale_price'      => 'required|integer|min:0',
            'stock_limit'           => 'required|integer|min:1',
            'max_purchase_per_user' => 'nullable|integer|min:1',
            'sort_order'            => 'nullable|integer',
        ]);

        $product = Product::find($request->product_id);

        FlashSaleItem::create([
            'flash_sale_id'         => $flashSale->id,
            'product_id'            => $product->id,
            'product_name'          => $product->name,
            'product_sku'           => $product->sku,
            'product_image_url'     => $product->primaryImage?->image_url,
            'original_price'        => $product->price,
            'sale_price'            => $request->flash_sale_price,
            'discount_type'         => 'fixed',
            'discount_value'        => $product->price - $request->flash_sale_price,
            'discount_percentage'   => round((($product->price - $request->flash_sale_price) / $product->price) * 100),
            'stock_limit'           => $request->stock_limit,
            'max_purchase_per_user' => $request->max_purchase_per_user ?? 10,
            'is_active'             => true,
            'sort_order'            => $request->sort_order ?? 0,
        ]);

        return back()
            ->with('success', 'Produk berhasil ditambahkan ke flash sale');
    }

    /**
     * Hapus item dari flash sale
     */
    public function removeItem(FlashSaleItem $item)
    {
        $flashSaleId = $item->flash_sale_id;
        $item->delete();

        return back()
            ->with('success', 'Produk berhasil dihapus dari flash sale');
    }
}
