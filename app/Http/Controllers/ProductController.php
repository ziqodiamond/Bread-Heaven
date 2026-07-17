<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * List all products
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category'])
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::all();
        $statuses = ['available', 'not_available'];

        return view('admin.management.product.index', compact('products', 'categories', 'statuses'));
    }

    /**
     * Edit product form
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $discountTypes = ['percent', 'fixed'];
        $statuses = ['available', 'not_available'];

        return view('admin.management.product.edit', compact('product', 'categories', 'discountTypes', 'statuses'));
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'sku' => 'required|string|max:255|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'sale_price' => 'nullable|integer|min:0',
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_max' => 'nullable|integer|min:0',
            'discount_label' => 'nullable|string|max:255',
            'discount_start_at' => 'nullable|date',
            'discount_end_at' => 'nullable|date|after:discount_start_at',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|integer|min:0',
            'length' => 'nullable|integer|min:0',
            'width' => 'nullable|integer|min:0',
            'height' => 'nullable|integer|min:0',
            'status' => 'required|in:available,not_available',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Buat product
        $product = Product::create($validated);

        // Handle images
        $this->storeProductImages($product, $request);

        return redirect()
            ->route('admin.management.products.index')
            ->with('success', 'Produk berhasil dibuat');
    }

    /**
     * Update product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id . ',id',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id . ',id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'sale_price' => 'nullable|integer|min:0',
            'discount_type' => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_max' => 'nullable|integer|min:0',
            'discount_label' => 'nullable|string|max:255',
            'discount_start_at' => 'nullable|date',
            'discount_end_at' => 'nullable|date|after:discount_start_at',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|integer|min:0',
            'length' => 'nullable|integer|min:0',
            'width' => 'nullable|integer|min:0',
            'height' => 'nullable|integer|min:0',
            'status' => 'required|in:available,not_available',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Update product
        $product->update($validated);

        // Handle images
        $this->storeProductImages($product, $request);

        return redirect()
            ->route('admin.management.products.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        // Delete images
        $product->images()->each(function ($image) {
            Storage::delete('public/' . $image->image_url);
            $image->delete();
        });

        // Delete product
        $product->delete();

        return redirect()
            ->route('admin.management.products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    /**
     * Store product images
     */
    private function storeProductImages(Product $product, Request $request)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $key => $image) {
                $path = $image->store('products', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $path,
                    'sort_order' => $key,
                    'is_primary' => $key === 0,
                ]);
            }
        }
    }
}
