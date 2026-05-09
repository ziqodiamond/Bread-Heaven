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
        $cart = Cart::with('items.product')
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
        $cart = Cart::with('items.product')
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

            return back()->with(
                'error',
                'Stok produk tidak mencukupi.'
            );
        }

        // Update quantity
        $cartItem->updateQuantity($request->quantity);

        return back()->with(
            'success',
            'Cart berhasil diperbarui.'
        );
    }

    /**
     * Hapus item cart
     */
    public function removeItem(string $id)
    {
        $cartItem = CartItem::findOrFail($id);

        $cartItem->delete();

        return back()->with(
            'success',
            'Item berhasil dihapus dari cart.'
        );
    }
}
