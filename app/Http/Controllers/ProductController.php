<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Product::select('category')->distinct()->pluck('category');
        $statuses = Product::select('status')->distinct()->pluck('status');

        $query = Product::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->get();

        return view('admin.product', compact('products', 'categories', 'statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'status' => 'required|in:available,not available',
            'image_url' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        // Simpan gambar di direktori product_images
        $imagePath = $request->file('image_url')->store('product_images', 'public');

        // Buat produk baru
        Product::create([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => $request->status,
            'image_url' => $imagePath,
        ]);

        return redirect()->back()->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'status' => 'required|in:available,not available',
            'image_url' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        // Periksa jika ada gambar baru
        if ($request->hasFile('image_url')) {
            // Hapus gambar lama jika ada
            if ($product->image_url && Storage::exists($product->image_url)) {
                Storage::delete($product->image_url);
            }
            // Simpan gambar baru di direktori product_images
            $imagePath = $request->file('image_url')->store('product_images', 'public');
            $product->image_url = $imagePath;
        }

        // Update produk
        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'status' => $request->status,
            'image_url' => $product->image_url ?? $product->getOriginal('image_url'),
        ]);

        return redirect()->back()->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Hapus gambar jika ada
        if ($product->image_url && Storage::exists($product->image_url)) {
            Storage::delete($product->image_url);
        }

        $product->delete();

        return response()->json(['success' => 'Product deleted successfully.']);
    }
}
