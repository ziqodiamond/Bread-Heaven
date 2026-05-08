<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\DeliveryMethod;
use App\Models\TransactionDetail;

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

    public function proceed(Request $request)
    {
        // Validasi data yang dikirimkan
        $validatedData = $request->validate([
            'delivery-method' => 'required|exists:delivery_methods,id',
            'payment-method' => 'required|exists:payment_methods,id',
            'nama' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'provinsi' => 'required|string',
            'kota' => 'required|string',
            'kode_pos' => 'required|string',
            'address' => 'required|string',
            'total_price' => 'required|numeric',
        ]);

        // Ambil keranjang berdasarkan user yang login
        $cart = Cart::where('user_id', auth()->id())->first();

        // Periksa apakah keranjang tersedia
        if (!$cart) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong atau tidak ditemukan.');
        }

        // Simpan cart_id ke dalam session
        $request->session()->put('cart_id', $cart->id);

        // Ambil cart items menggunakan cart_id dari session
        $cartItems = CartItem::with('product')
            ->where('cart_id', $request->session()->get('cart_id'))
            ->get();

        // Cek apakah cart items kosong
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        // Buat string alamat lengkap
        $fullAddress = $validatedData['address'] . ', ' . $validatedData['kota'] . ', ' . $validatedData['provinsi'] . ' ' . $validatedData['kode_pos'];

        // Ambil ID pengguna yang sedang login
        $userId = auth()->id();

        // Buat transaksi baru
        $transaction = Transaction::create([
            'user_id' => $userId,
            'payment_method' => $validatedData['payment-method'],
            'delivery_method' => $validatedData['delivery-method'],
            'customer_name' => $validatedData['nama'],
            'customer_phone' => $validatedData['phone'],
            'customer_email' => $validatedData['email'],
            'customer_address' => $fullAddress,
            'total_price' => $validatedData['total_price'],
            'status' => 'pending',
        ]);

        // Buat detail transaksi
        foreach ($cartItems as $item) {
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
                'subtotal' => $item->quantity * $item->product->price,
            ]);
        }

        // Hapus cart_id dari session setelah checkout berhasil
        $request->session()->forget('cart_id');
        $request->session()->regenerate();


        // Redirect ke halaman sukses

        // Redirect to the transaction details page
        return redirect()->route('transaction.show', $transaction->id)->with('success', 'Your order has been placed successfully.');
    }
}
