<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        // Mengambil cart berdasarkan user yang sedang login
        $cart = Cart::where('user_id', auth()->id())
            ->with('items.product') // Memuat relasi items dan produk terkait
            ->firstOrCreate([
                'user_id' => auth()->id(),
            ]);

        // Menghitung total harga dari cart
        $totalPrice = $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // Menghitung total harga sebelum diskon, pajak, dan biaya pengiriman
        $originalPrice = $totalPrice + 299; // Contoh diskon sebesar $299

        // Simpan data untuk tampilan
        return view('cart.index', [
            'cart' => $cart,
            'totalPrice' => $totalPrice,
            'originalPrice' => $originalPrice,
            'savings' => 299, // Contoh nilai diskon
            'tax' => 799, // Contoh nilai pajak
            'pickupFee' => 99 // Contoh biaya pengiriman
        ]);
    }


    public function show()
    {
        $cart = Cart::where('user_id', auth()->id())
            ->with('items.product') // Memuat relasi items dan produk terkait
            ->firstOrCreate([
                'user_id' => auth()->id(),
            ]);

        return view('cart.modal-cart', compact('cart'));
    }



    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);

        // Temukan atau buat cart untuk user yang sedang login
        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        // Tambahkan produk ke cart
        $cart->items()->create([
            'product_id' => $productId,
            'quantity' => 1, // Set jumlah item, bisa ditingkatkan sesuai kebutuhan
        ]);

        return redirect()->back()->with('success', 'Product added to cart successfully.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $cartItem->update(['quantity' => $request->quantity]);

        return redirect()->back()->with('success', 'Cart updated successfully!');
    }

    public function removeItem($id)
    {
        $cartItem = CartItem::find($id);

        if ($cartItem) {
            $cartItem->delete();
            return redirect()->back()->with('success', 'Item has been removed from the cart.');
        }

        return redirect()->back()->with('error', 'Item not found.');
    }
}
