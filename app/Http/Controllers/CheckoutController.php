<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\DeliveryMethod;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = auth()->user(); // Mengambil data user yang sedang login
        $cart = Cart::where('user_id', $user->id)->first();
        $cartItems = CartItem::where('cart_id', $cart->id)->get();
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $deliveryMethods = DeliveryMethod::where('status', 'available')->get();
        $paymentMethods = PaymentMethod::where('status', 'available')->get();

        return view('checkout', compact('cartItems', 'totalPrice', 'deliveryMethods', 'paymentMethods'));
    }

    public function proceedToPayment(Request $request)
    {
        // Logic untuk melanjutkan ke halaman pembayaran
        // Bisa termasuk validasi data checkout, menyimpan transaksi, dll.

        return redirect()->route('payment.show');
    }
}
