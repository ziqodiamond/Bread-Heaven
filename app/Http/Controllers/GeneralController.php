<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\FlashSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralController extends Controller
{
    public function index()
    {
        $products = Product::paginate(30);
        $cartItems = Auth::check() && Auth::user()->cart ? Auth::user()->cart->items : collect();
        
        // Get active flash sales for homepage
        $flashSales = FlashSale::homepage()
            ->active()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->with('items')
            ->get();

        // dd($cartItems); // Debugging data

        return view('home',  [
            'cartItems' => $cartItems,
            'products' => $products,
            'flashSales' => $flashSales,
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

    /**
     * Get flash sales status for API
     */
    public function getFlashSalesStatus()
    {
        $flashSales = FlashSale::homepage()
            ->active()
            ->get();

        $statuses = $flashSales->map(function ($flashSale) {
            return [
                'id' => $flashSale->id,
                'name' => $flashSale->name,
                'slug' => $flashSale->slug,
                'start_at' => $flashSale->start_at->timestamp,
                'end_at' => $flashSale->end_at->timestamp,
                'is_running' => $flashSale->is_running,
                'has_ended' => $flashSale->has_ended,
                'has_started' => $flashSale->has_started,
                'remaining_seconds' => $flashSale->remaining_seconds,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $statuses,
        ]);
    }
}

