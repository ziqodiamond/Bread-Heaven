<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $categories = Product::select('category')->distinct()->pluck('category');
        $statuses   = Product::select('status')->distinct()->pluck('status');

        $query = Product::with('images'); // eager load semua images sekaligus

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->get();

        return view('admin.management.product.index', compact(
            'products',
            'categories',
            'statuses',
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'category'            => 'required|string|max:255',
            'description'         => 'required|string',
            'price'               => 'required|integer|min:0',
            'stock'               => 'required|integer|min:0',
            'weight'              => 'required|integer|min:0',
            'length'              => 'nullable|integer|min:0',
            'width'               => 'nullable|integer|min:0',
            'height'              => 'nullable|integer|min:0',
            'status'              => 'required|in:available,not_available',
            'images'              => 'required|array|min:1',
            'images.*'            => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'primary_image_index' => 'nullable|integer|min:0',
        ]);

        $files        = $request->file('images');
        $primaryIndex = (int) $request->input('primary_image_index', 0);
        $primaryIndex = min($primaryIndex, count($files) - 1); // clamp agar tidak out of bounds

        DB::transaction(function () use ($request, $files, $primaryIndex) {

            $product = Product::create([
                'name'        => $request->name,
                'slug'        => Str::slug($request->name) . '-' . Str::random(5),
                'sku'         => 'SKU-' . strtoupper(Str::random(10)),
                'category'    => $request->category,
                'description' => $request->description,
                'price'       => $request->price,
                'stock'       => $request->stock,
                'weight'      => $request->weight,
                'length'      => $request->length ?? 0,
                'width'       => $request->width ?? 0,
                'height'      => $request->height ?? 0,
                'status'      => $request->status,
            ]);

            foreach ($files as $index => $image) {
                $path = $image->store('product_images', 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url'  => $path,
                    'alt_text'   => $product->name,
                    'sort_order' => $index + 1,
                    'is_primary' => $index === $primaryIndex,
                    'is_active'  => true,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    | Alur:
    |   1. Update data produk
    |   2. Hapus gambar yang di-mark deleted (hapus file + record)
    |   3. Upload gambar baru
    |   4. Reset semua is_primary → false
    |   5. Set primary:
    |        a. Jika primary_image_id terisi → set existing image
    |        b. Jika primary_new_image_index >= 0 → set gambar baru berdasarkan urutan upload
    |        c. Fallback otomatis ke gambar pertama
    */

    public function update(Request $request, Product $product)
    {
        // Normalisasi nilai kosong/null dari hidden input Alpine
        foreach (['primary_image_id', 'primary_new_image_index'] as $field) {
            if (in_array($request->input($field), ['', 'null', 'undefined', null], true)) {
                $request->merge([$field => null]);
            }
        }

        $request->validate([
            'name'                    => 'required|string|max:255',
            'category'                => 'required|string|max:255',
            'description'             => 'required|string',
            'price'                   => 'required|integer|min:0',
            'stock'                   => 'required|integer|min:0',
            'weight'                  => 'required|integer|min:0',
            'length'                  => 'nullable|integer|min:0',
            'width'                   => 'nullable|integer|min:0',
            'height'                  => 'nullable|integer|min:0',
            'status'                  => 'required|in:available,not_available',
            'images'                  => 'nullable|array',
            'images.*'                => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'deleted_images'          => 'nullable|array',
            'deleted_images.*'        => 'uuid|exists:product_images,id',
            'primary_image_id'        => 'nullable|uuid|exists:product_images,id',
            'primary_new_image_index' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $product) {

            /*
            | 1. Update data produk
            */
            $product->update([
                'name'        => $request->name,
                'slug'        => Str::slug($request->name) . '-' . Str::random(5),
                'category'    => $request->category,
                'description' => $request->description,
                'price'       => $request->price,
                'stock'       => $request->stock,
                'weight'      => $request->weight,
                'length'      => $request->length ?? 0,
                'width'       => $request->width ?? 0,
                'height'      => $request->height ?? 0,
                'status'      => $request->status,
            ]);

            /*
            | 2. Hapus gambar yang dipilih user untuk dihapus
            */
            if ($request->filled('deleted_images')) {
                $toDelete = ProductImage::whereIn('id', $request->deleted_images)
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($toDelete as $img) {
                    Storage::disk('public')->delete($img->image_url);
                    $img->delete();
                }
            }

            /*
            | 3. Upload gambar baru & simpan urutan sort_order-nya
            |    Kita perlu tahu ID gambar baru berdasarkan primaryNewIndex
            */
            $newImageIds = [];

            if ($request->hasFile('images')) {
                $lastSort = ProductImage::where('product_id', $product->id)
                    ->max('sort_order') ?? 0;

                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('product_images', 'public');

                    $newImg = ProductImage::create([
                        'product_id' => $product->id,
                        'image_url'  => $path,
                        'alt_text'   => $product->name,
                        'sort_order' => $lastSort + $index + 1,
                        'is_primary' => false, // set nanti di langkah 5
                        'is_active'  => true,
                    ]);

                    $newImageIds[] = $newImg->id;
                }
            }

            /*
            | 4. Reset semua primary → false
            */
            ProductImage::where('product_id', $product->id)
                ->update(['is_primary' => false]);

            /*
            | 5. Set primary
            |    Prioritas: existing > new > fallback
            */
            $primarySet = false;

            // 5a. Primary dari existing image
            if ($request->filled('primary_image_id')) {
                $affected = ProductImage::where('id', $request->primary_image_id)
                    ->where('product_id', $product->id)
                    ->update(['is_primary' => true]);

                $primarySet = $affected > 0;
            }

            // 5b. Primary dari gambar baru (berdasarkan index urutan upload)
            if (!$primarySet && $request->filled('primary_new_image_index')) {
                $newIndex = (int) $request->primary_new_image_index;

                if (isset($newImageIds[$newIndex])) {
                    ProductImage::where('id', $newImageIds[$newIndex])
                        ->update(['is_primary' => true]);

                    $primarySet = true;
                }
            }

            // 5c. Fallback: set gambar pertama yang masih ada sebagai primary
            if (!$primarySet) {
                ProductImage::where('product_id', $product->id)
                    ->orderBy('sort_order')
                    ->first()
                    ?->update(['is_primary' => true]);
            }
        });

        return redirect()->back()->with('success', 'Produk berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {

            // Hapus semua file gambar dari storage
            foreach ($product->images as $image) {

                Storage::disk('public')
                    ->delete($image->image_url);
            }

            /*
        |--------------------------------------------------------------------------
        | Cek apakah produk pernah dipakai di order
        |--------------------------------------------------------------------------
        |
        | Jika pernah diorder:
        | -> soft delete
        |
        | Jika belum pernah diorder:
        | -> hard delete untuk penghematan database
        |
        */

            $hasOrder = $product->orderItems()->exists();

            if ($hasOrder) {

                // Soft delete
                $product->delete();
            } else {

                // Hard delete permanen
                $product->forceDelete();
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Produk berhasil dihapus.');
    }
}
