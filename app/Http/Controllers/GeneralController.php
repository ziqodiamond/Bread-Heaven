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
        $products = Product::latest()
            ->limit(12)
            ->paginate(12);
        
        $cartItems = Auth::check() && Auth::user()->cart ? Auth::user()->cart->items : collect();
        
        // Get active flash sales for homepage
        $flashSales = FlashSale::homepage()
            ->active()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->with('items')
            ->get();

        return view('home',  [
            'cartItems' => $cartItems,
            'products' => $products,
            'flashSales' => $flashSales,
        ]);
    }


    public function products(Request $request)
    {
        $query = Product::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // Discount filter
        if ($request->filled('discount_type')) {
            $discountType = $request->input('discount_type');
            if ($discountType === 'flash_sale') {
                $query->where('active_discount_type', 'flash_sale');
            } elseif ($discountType === 'discount') {
                $query->where('active_discount_type', 'discount');
            } elseif ($discountType === 'none') {
                $query->where('active_discount_type', 'none');
            }
        }

        // Sort options
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12);
        $cartItems = Auth::check() && Auth::user()->cart ? Auth::user()->cart->items : collect();

        // Get all available categories for filter dropdown
        $categories = Product::distinct('category')
            ->where('category', '!=', null)
            ->pluck('category');

        return view('products', [
            'cartItems' => $cartItems,
            'products' => $products,
            'categories' => $categories,
            'search' => $request->input('search', ''),
            'category' => $request->input('category', ''),
            'min_price' => $request->input('min_price', ''),
            'max_price' => $request->input('max_price', ''),
            'discount_type' => $request->input('discount_type', ''),
            'sort' => $sort,
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


