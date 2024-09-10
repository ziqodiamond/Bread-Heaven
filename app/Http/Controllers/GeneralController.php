<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralController extends Controller
{
    public function index()
    {
        $products = Product::paginate(30);
        $cartItems = Auth::check() && Auth::user()->cart ? Auth::user()->cart->items : collect();

        // dd($cartItems); // Debugging data

        return view('home',  [
            'cartItems' => $cartItems,
            'products' => $products,
        ]);
    }


    public function products()
    {
        $products = Product::paginate(30);
        $cartItems = Auth::check() && Auth::user()->cart ? Auth::user()->cart->items : collect();

        // dd($cartItems); // Debugging data

        return view('products', [
            'cartItems' => $cartItems,
            'products' => $products,
        ]);
    }

    public function show($id)
    {
        // Mengambil data produk berdasarkan ID
        $product = Product::findOrFail($id);

        // Mengembalikan view dengan data produk
        return view('item', compact('product'));
    }
}
