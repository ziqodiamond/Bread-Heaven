<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function index()
    {
        // Tanggal hari ini
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | Order hari ini
        |--------------------------------------------------------------------------
        */

        $ordersToday = Order::with([
            'user',
            'items.product',
        ])
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Statistik utama
        |--------------------------------------------------------------------------
        */

        // Total order hari ini
        $totalOrders = $ordersToday->count();

        // Total item terjual
        $totalItemsSold = $ordersToday->sum(function ($order) {
            return $order->items->sum('quantity');
        });

        // Total revenue
        $totalRevenue = $ordersToday->sum('grand_total');

        // Total customer
        $totalCustomers = User::where('role', 'customer')->count();

        // Total produk
        $totalProducts = Product::count();

        /*
        |--------------------------------------------------------------------------
        | Statistik order
        |--------------------------------------------------------------------------
        */

        $pendingOrders = Order::where('order_status', 'pending')->count();

        $processingOrders = Order::where('order_status', 'processing')->count();

        $shippedOrders = Order::where('order_status', 'shipped')->count();

        $completedOrders = Order::where('order_status', 'completed')->count();

        $cancelledOrders = Order::where('order_status', 'cancelled')->count();

        /*
        |--------------------------------------------------------------------------
        | Statistik pembayaran
        |--------------------------------------------------------------------------
        */

        $paidOrders = Order::where('payment_status', 'paid')->count();

        $unpaidOrders = Order::where('payment_status', 'unpaid')->count();

        $expiredOrders = Order::where('payment_status', 'expired')->count();

        /*
        |--------------------------------------------------------------------------
        | Statistik stok
        |--------------------------------------------------------------------------
        */

        // Produk stok menipis
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->where('stock', '>', 0)
            ->count();

        // Produk habis
        $outOfStockProducts = Product::where('stock', 0)->count();

        /*
        |--------------------------------------------------------------------------
        | Return view
        |--------------------------------------------------------------------------
        */

        return view('admin.dashboard', [

            // Statistik utama
            'totalOrders' => $totalOrders,
            'totalItemsSold' => $totalItemsSold,
            'totalRevenue' => $totalRevenue,
            'totalCustomers' => $totalCustomers,
            'totalProducts' => $totalProducts,

            // Statistik order
            'pendingOrders' => $pendingOrders,
            'processingOrders' => $processingOrders,
            'shippedOrders' => $shippedOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,

            // Statistik pembayaran
            'paidOrders' => $paidOrders,
            'unpaidOrders' => $unpaidOrders,
            'expiredOrders' => $expiredOrders,

            // Statistik stok
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,

            // Order hari ini
            'ordersToday' => $ordersToday,
        ]);
    }
}
