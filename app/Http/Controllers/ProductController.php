<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Display Listing
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $products = Product::with([
            'images',
            'primaryImage',
        ])

            ->latest()

            ->paginate(10);

        return view(
            'admin.management.product.index',
            compact('products')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Show Create Form
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.management.product.create');
    }

    /*
    |--------------------------------------------------------------------------
    | Store Product
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Informasi Produk
            |--------------------------------------------------------------------------
            */

            'name' => 'required|string|max:255',

            'category' => 'nullable|string|max:255',

            'description' => 'nullable|string',

            /*
            |--------------------------------------------------------------------------
            | Harga
            |--------------------------------------------------------------------------
            */

            'price' => 'required|integer|min:0',

            /*
            |--------------------------------------------------------------------------
            | Discount
            |--------------------------------------------------------------------------
            */

            'discount_label' => 'nullable|string|max:255',

            'discount_type' => 'nullable|in:fixed,percent',

            'discount_value' => 'nullable|numeric|min:0',

            'discount_start_at' => 'nullable|date',

            'discount_end_at' => 'nullable|date|after_or_equal:discount_start_at',

            /*
            |--------------------------------------------------------------------------
            | Stock
            |--------------------------------------------------------------------------
            */

            'stock' => 'required|integer|min:0',

            /*
            |--------------------------------------------------------------------------
            | Shipping
            |--------------------------------------------------------------------------
            */

            'weight' => 'required|integer|min:0',

            'length' => 'nullable|integer|min:0',

            'width' => 'nullable|integer|min:0',

            'height' => 'nullable|integer|min:0',

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' => 'required|in:available,draft,archived',

            /*
            |--------------------------------------------------------------------------
            | Images
            |--------------------------------------------------------------------------
            */

            'images.*' => 'nullable|image|max:5120',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi bisnis discount
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('discount_type') &&
            !$request->filled('discount_value')
        ) {

            return back()

                ->withErrors([
                    'discount_value' =>
                    'Nilai diskon wajib diisi.',
                ])

                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Discount percent maksimal 100
        |--------------------------------------------------------------------------
        */

        if (
            $request->discount_type === 'percent' &&
            $request->discount_value > 100
        ) {

            return back()

                ->withErrors([
                    'discount_value' =>
                    'Diskon persen maksimal 100%.',
                ])

                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Discount fixed tidak boleh melebihi harga
        |--------------------------------------------------------------------------
        */

        if (
            $request->discount_type === 'fixed' &&
            $request->discount_value >= $request->price
        ) {

            return back()

                ->withErrors([
                    'discount_value' =>
                    'Diskon nominal tidak boleh melebihi harga produk.',
                ])

                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Enable discount
        |--------------------------------------------------------------------------
        */

        $discountEnabled =
            $request->boolean('enable_discount');

        /*
        |--------------------------------------------------------------------------
        | Hitung sale price otomatis
        |--------------------------------------------------------------------------
        */

        $salePrice = null;

        if ($discountEnabled) {

            /*
            |--------------------------------------------------------------------------
            | Fixed discount
            |--------------------------------------------------------------------------
            */

            if (
                $request->discount_type === 'fixed'
            ) {

                $salePrice =
                    max(
                        0,
                        $request->price -
                            $request->discount_value
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Percent discount
            |--------------------------------------------------------------------------
            */ elseif (
                $request->discount_type === 'percent'
            ) {

                $salePrice =
                    $request->price -
                    floor(
                        (
                            $request->price *
                            $request->discount_value
                        ) / 100
                    );
            }
        }

        DB::transaction(function () use (
            $request,
            $discountEnabled,
            $salePrice
        ) {

            /*
            |--------------------------------------------------------------------------
            | Generate SKU unik
            |--------------------------------------------------------------------------
            */

            do {

                $sku =
                    'SKU-' .
                    strtoupper(
                        Str::random(10)
                    );
            } while (
                Product::where(
                    'sku',
                    $sku
                )->exists()
            );

            /*
            |--------------------------------------------------------------------------
            | Create product
            |--------------------------------------------------------------------------
            */

            $product = Product::create([

                /*
                |--------------------------------------------------------------------------
                | Informasi Produk
                |--------------------------------------------------------------------------
                */

                'name' => $request->name,

                'slug' =>
                Str::slug($request->name) .
                    '-' .
                    Str::lower(
                        Str::random(5)
                    ),

                'sku' => $sku,

                'category' => $request->category,

                'description' => $request->description,

                /*
                |--------------------------------------------------------------------------
                | Harga
                |--------------------------------------------------------------------------
                */

                'price' => $request->price,

                /*
                |--------------------------------------------------------------------------
                | Discount
                |--------------------------------------------------------------------------
                */

                'sale_price' =>
                $discountEnabled
                    ? $salePrice
                    : null,

                'discount_label' =>
                $discountEnabled
                    ? $request->discount_label
                    : null,

                'discount_type' =>
                $discountEnabled
                    ? $request->discount_type
                    : null,

                'discount_value' =>
                $discountEnabled
                    ? $request->discount_value
                    : null,

                'discount_start_at' =>
                $discountEnabled
                    ? $request->discount_start_at
                    : null,

                'discount_end_at' =>
                $discountEnabled
                    ? $request->discount_end_at
                    : null,

                /*
                |--------------------------------------------------------------------------
                | Stock
                |--------------------------------------------------------------------------
                */

                'stock' => $request->stock,

                /*
                |--------------------------------------------------------------------------
                | Shipping
                |--------------------------------------------------------------------------
                */

                'weight' => $request->weight,

                'length' => $request->length,

                'width' => $request->width,

                'height' => $request->height,

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                'status' => $request->status,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Upload images
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('images')) {

                foreach (
                    $request->file('images')
                    as $index => $image
                ) {

                    $path = $image->store(
                        'products',
                        'public'
                    );

                    ProductImage::create([

                        'product_id' =>
                        $product->id,

                        'image_url' => $path,

                        'alt_text' =>
                        $product->name,

                        'sort_order' => $index,

                        'is_primary' =>
                        $index === 0,

                        'is_active' => true,
                    ]);
                }
            }
        });

        return redirect()

            ->route('admin.management.products.index')

            ->with(
                'success',
                'Produk berhasil ditambahkan.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Show Edit Form
    |--------------------------------------------------------------------------
    */

    public function edit(Product $product)
    {
        $product->load([
            'images',
            'primaryImage',
        ]);

        return view(
            'products.edit',
            compact('product')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Product
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Product $product
    ) {

        /*
        |--------------------------------------------------------------------------
        | Normalisasi hidden input
        |--------------------------------------------------------------------------
        */

        foreach (
            [
                'primary_image_id',
                'primary_new_image_index',
            ]
            as $field
        ) {

            if (
                in_array(
                    $request->input($field),
                    [
                        '',
                        'null',
                        'undefined',
                        null,
                    ],
                    true
                )
            ) {

                $request->merge([
                    $field => null,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'name' =>
            'required|string|max:255',

            'category' =>
            'nullable|string|max:255',

            'description' =>
            'nullable|string',

            'price' =>
            'required|integer|min:0',

            'discount_label' =>
            'nullable|string|max:255',

            'discount_type' =>
            'nullable|in:fixed,percent',

            'discount_value' =>
            'nullable|numeric|min:0',

            'discount_start_at' =>
            'nullable|date',

            'discount_end_at' =>
            'nullable|date|after_or_equal:discount_start_at',

            'stock' =>
            'required|integer|min:0',

            'weight' =>
            'required|integer|min:0',

            'length' =>
            'nullable|integer|min:0',

            'width' =>
            'nullable|integer|min:0',

            'height' =>
            'nullable|integer|min:0',

            'status' =>
            'required|in:available,draft,archived',

            'images.*' =>
            'nullable|image|max:5120',

            'delete_images' =>
            'nullable|array',

            'delete_images.*' =>
            'exists:product_images,id',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi bisnis discount
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('discount_type') &&
            !$request->filled('discount_value')
        ) {

            return back()

                ->withErrors([
                    'discount_value' =>
                    'Nilai diskon wajib diisi.',
                ])

                ->withInput();
        }

        if (
            $request->discount_type === 'percent' &&
            $request->discount_value > 100
        ) {

            return back()

                ->withErrors([
                    'discount_value' =>
                    'Diskon persen maksimal 100%.',
                ])

                ->withInput();
        }

        if (
            $request->discount_type === 'fixed' &&
            $request->discount_value >= $request->price
        ) {

            return back()

                ->withErrors([
                    'discount_value' =>
                    'Diskon nominal tidak boleh melebihi harga produk.',
                ])

                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Enable discount
        |--------------------------------------------------------------------------
        */

        $discountEnabled =
            $request->boolean('enable_discount');

        /*
        |--------------------------------------------------------------------------
        | Hitung sale price otomatis
        |--------------------------------------------------------------------------
        */

        $salePrice = null;

        if ($discountEnabled) {

            if (
                $request->discount_type === 'fixed'
            ) {

                $salePrice =
                    max(
                        0,
                        $request->price -
                            $request->discount_value
                    );
            } elseif (
                $request->discount_type === 'percent'
            ) {

                $salePrice =
                    $request->price -
                    floor(
                        (
                            $request->price *
                            $request->discount_value
                        ) / 100
                    );
            }
        }

        DB::transaction(function () use (
            $request,
            $product,
            $discountEnabled,
            $salePrice
        ) {

            /*
            |--------------------------------------------------------------------------
            | Update product
            |--------------------------------------------------------------------------
            */

            $product->update([

                'name' => $request->name,

                /*
                |--------------------------------------------------------------------------
                | Slug tidak berubah
                |--------------------------------------------------------------------------
                */

                'category' =>
                $request->category,

                'description' =>
                $request->description,

                'price' =>
                $request->price,

                /*
                |--------------------------------------------------------------------------
                | Discount
                |--------------------------------------------------------------------------
                */

                'sale_price' =>
                $discountEnabled
                    ? $salePrice
                    : null,

                'discount_label' =>
                $discountEnabled
                    ? $request->discount_label
                    : null,

                'discount_type' =>
                $discountEnabled
                    ? $request->discount_type
                    : null,

                'discount_value' =>
                $discountEnabled
                    ? $request->discount_value
                    : null,

                'discount_start_at' =>
                $discountEnabled
                    ? $request->discount_start_at
                    : null,

                'discount_end_at' =>
                $discountEnabled
                    ? $request->discount_end_at
                    : null,

                /*
                |--------------------------------------------------------------------------
                | Stock
                |--------------------------------------------------------------------------
                */

                'stock' =>
                $request->stock,

                /*
                |--------------------------------------------------------------------------
                | Shipping
                |--------------------------------------------------------------------------
                */

                'weight' =>
                $request->weight,

                'length' =>
                $request->length,

                'width' =>
                $request->width,

                'height' =>
                $request->height,

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                'status' =>
                $request->status,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Delete selected images
            |--------------------------------------------------------------------------
            */

            if (
                $request->filled(
                    'delete_images'
                )
            ) {

                $images =
                    ProductImage::whereIn(
                        'id',
                        $request->delete_images
                    )->get();

                foreach ($images as $image) {

                    Storage::disk('public')
                        ->delete(
                            $image->image_url
                        );

                    $image->delete();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Reset primary image
            |--------------------------------------------------------------------------
            */

            ProductImage::where(
                'product_id',
                $product->id
            )->update([
                'is_primary' => false,
            ]);

            $primarySet = false;

            /*
            |--------------------------------------------------------------------------
            | Existing image jadi primary
            |--------------------------------------------------------------------------
            */

            if (
                $request->filled(
                    'primary_image_id'
                )
            ) {

                ProductImage::where(
                    'id',
                    $request->primary_image_id
                )->update([
                    'is_primary' => true,
                ]);

                $primarySet = true;
            }

            /*
            |--------------------------------------------------------------------------
            | Upload new images
            |--------------------------------------------------------------------------
            */

            if (
                $request->hasFile('images')
            ) {

                $lastSortOrder =
                    ProductImage::where(
                        'product_id',
                        $product->id
                    )->max('sort_order') ?? -1;

                foreach (
                    $request->file('images')
                    as $index => $image
                ) {

                    $path = $image->store(
                        'products',
                        'public'
                    );

                    $newImage =
                        ProductImage::create([

                            'product_id' =>
                            $product->id,

                            'image_url' =>
                            $path,

                            'alt_text' =>
                            $product->name,

                            'sort_order' =>
                            $lastSortOrder +
                                $index +
                                1,

                            'is_primary' =>
                            false,

                            'is_active' =>
                            true,
                        ]);

                    /*
                    |--------------------------------------------------------------------------
                    | New image jadi primary
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !$primarySet &&
                        $request->primary_new_image_index !== null &&
                        (int) $request->primary_new_image_index === $index
                    ) {

                        $newImage->update([
                            'is_primary' => true,
                        ]);

                        $primarySet = true;
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback primary image
            |--------------------------------------------------------------------------
            */

            if (!$primarySet) {

                $firstImage =
                    ProductImage::where(
                        'product_id',
                        $product->id
                    )

                    ->orderBy('sort_order')

                    ->first();

                if ($firstImage) {

                    $firstImage->update([
                        'is_primary' => true,
                    ]);
                }
            }
        });

        return redirect()

            ->route('admin.management.products.index')

            ->with(
                'success',
                'Produk berhasil diperbarui.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Product
    |--------------------------------------------------------------------------
    */

    public function destroy(Product $product)
    {
        DB::transaction(function () use (
            $product
        ) {

            /*
            |--------------------------------------------------------------------------
            | Delete image files
            |--------------------------------------------------------------------------
            */

            foreach (
                $product->images
                as $image
            ) {

                Storage::disk('public')
                    ->delete(
                        $image->image_url
                    );

                $image->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | Jika pernah diorder
            | gunakan soft delete
            |--------------------------------------------------------------------------
            */

            $hasOrder =
                $product->orderItems()
                ->exists();

            if ($hasOrder) {

                $product->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | Jika belum pernah diorder
            | hard delete
            |--------------------------------------------------------------------------
            */ else {

                $product->forceDelete();
            }
        });

        return redirect()

            ->route('products.index')

            ->with(
                'success',
                'Produk berhasil dihapus.'
            );
    }
}
