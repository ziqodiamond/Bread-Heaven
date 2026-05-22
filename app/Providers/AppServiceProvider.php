<?php

namespace App\Providers;

use App\Models\Cart;
use App\Helpers\ColorHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Membuat $cartItems tersedia di semua view
        View::composer('*', function ($view) {
            // Pastikan user terautentikasi
            if (Auth::check()) {
                $user = Auth::user();
                $cart = Cart::where('user_id', $user->id)->with('items.product')->first();
                $cartItems = $cart ? $cart->items : collect();
            } else {
                $cartItems = collect();
            }

            $view->with('cartItems', $cartItems);
        });

        // Register helper function for color mapping
        if (!function_exists('getColorRGB')) {
            function getColorRGB($colorName, $type = 'start') {
                return ColorHelper::getColorRGB($colorName, $type);
            }
        }
    }
}
