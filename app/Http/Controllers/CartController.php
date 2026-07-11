<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Halaman cart
     */
    public function index()
    {
        $cart = Cart::with(['items.product.activeFlashSaleItem.flashSale'])
            ->firstOrCreate([
                'user_id' => auth()->id(),
            ]);

        return view('cart.index', compact('cart'));
    }

    /**
     * Modal cart
     */
    public function show()
    {
        $cart = Cart::with(['items.product.activeFlashSaleItem.flashSale'])
            ->firstOrCreate([
                'user_id' => auth()->id(),
            ]);

        return view('cart.modal-cart', compact('cart'));
    }

    /**
     * Tambah produk ke cart
     */
    public function add(Request $request)
    {
        // Ambil produk
        $product = Product::available()
            ->inStock()
            ->findOrFail($request->product_id);

        // Cari / buat cart user
        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id(),
        ], [
            'status' => 'active',
        ]);

        // Cek apakah produk sudah ada di cart
        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Jika produk sudah ada
        |--------------------------------------------------------------------------
        */
        if ($cartItem) {

            $newQuantity = $cartItem->quantity + 1;

            // Validasi stok
            if (!$product->hasEnoughStock($newQuantity)) {

                return back()->with(
                    'error',
                    'Stok produk tidak mencukupi.'
                );
            }

            // Update quantity
            $cartItem->updateQuantity($newQuantity);
        }

        /*
        |--------------------------------------------------------------------------
        | Jika produk belum ada
        |--------------------------------------------------------------------------
        */ else {

            $cart->items()->create([

                'product_id' => $product->id,

                'quantity' => 1,
            ]);
        }

        // Refresh summary cart
        $cart->refreshCartSummary();

        return back()->with(
            'success',
            'Produk berhasil ditambahkan ke cart.'
        );
    }

    /**
     * Update quantity cart item
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1']
        ]);

        $product = $cartItem->product;

        // Validasi stok
        if (!$product->hasEnoughStock($request->quantity)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok produk tidak mencukupi.'
                ], 422);
            }
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        // Update quantity
        $cartItem->updateQuantity($request->quantity);

        // Refresh cart for updated data
        $cart = $cartItem->cart;
        $cart->refreshCartSummary();

        // Revalidate vouchers against new cart
        $revalidation = app(\App\Services\VoucherService::class)->revalidateCartVouchers($cart);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart berhasil diperbarui.',
                'data' => [
                    'items' => $cart->items()->with('product')->get()->map(fn($item) => [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'product_image_url' => $item->product->thumbnail ?? null,
                        'quantity' => $item->quantity,
                        'product_price' => $item->product_price,
                        'original_price' => $item->original_price,
                        'subtotal' => $item->subtotal,
                        'discount_amount' => $item->discount_amount,
                    ]),
                    'summary' => [
                        'total_items' => $cart->total_items,
                        'total_quantity' => $cart->total_quantity,
                        'subtotal' => $cart->subtotal,
                        'discount_amount' => $cart->total_discount_amount ?? 0,
                        'final_subtotal' => $cart->final_subtotal,
                    ],
                    'invalid_vouchers' => $revalidation['removed'] ?? [],
                    'applied_vouchers' => $cart->getAppliedVouchers()->map(function($v) {
                        $va = (array) $v;
                        return [
                            'id' => $va['id'] ?? null,
                            'name' => $va['name'] ?? null,
                            'code' => $va['code'] ?? null,
                            'description' => $va['description'] ?? null,
                            'image_url' => $va['image_url'] ?? null,
                            'type' => $va['type'] ?? null,
                            'value' => $va['value'] ?? null,
                            'minimum_purchase' => $va['minimum_purchase'] ?? 0,
                        ];
                    })->toArray(),
                ],
            ]);
        }

        return back()->with('success', 'Cart berhasil diperbarui.');
    }

    /**
     * Hapus item cart
     */
    public function removeItem(Request $request, string $id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cart = $cartItem->cart;

        $cartItem->delete();
        $cart->refreshCartSummary();

        // Revalidate vouchers
        $revalidation = app(\App\Services\VoucherService::class)->revalidateCartVouchers($cart);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari cart.',
                'data' => [
                    'items' => $cart->items()->with('product')->get()->map(fn($item) => [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'product_image_url' => $item->product->thumbnail ?? null,
                        'quantity' => $item->quantity,
                        'product_price' => $item->product_price,
                        'original_price' => $item->original_price,
                        'subtotal' => $item->subtotal,
                        'discount_amount' => $item->discount_amount,
                    ]),
                    'summary' => [
                        'total_items' => $cart->total_items,
                        'total_quantity' => $cart->total_quantity,
                        'subtotal' => $cart->subtotal,
                        'discount_amount' => $cart->total_discount_amount ?? 0,
                        'final_subtotal' => $cart->final_subtotal,
                    ],
                    'invalid_vouchers' => $revalidation['removed'] ?? [],
                    'applied_vouchers' => $cart->getAppliedVouchers()->map(function($v) {
                        $va = (array) $v;
                        return [
                            'id' => $va['id'] ?? null,
                            'name' => $va['name'] ?? null,
                            'code' => $va['code'] ?? null,
                            'description' => $va['description'] ?? null,
                            'image_url' => $va['image_url'] ?? null,
                            'type' => $va['type'] ?? null,
                            'value' => $va['value'] ?? null,
                            'minimum_purchase' => $va['minimum_purchase'] ?? 0,
                        ];
                    })->toArray(),
                ],
            ]);
        }

        return back()->with('success', 'Item berhasil dihapus dari cart.');
    }
}
