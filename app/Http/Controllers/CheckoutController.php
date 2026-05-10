<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\Store;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | index — Halaman checkout
    |--------------------------------------------------------------------------
    | Mendukung dua mode:
    |   - buy_now : langsung beli dari halaman produk
    |   - cart    : dari keranjang belanja
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = auth()->user();

        /* ------------------------------------------------------------------ */
        /* BUY NOW MODE                                                         */
        /* ------------------------------------------------------------------ */
        if ($request->mode === 'buy_now') {

            $product = Product::with('primaryImage')
                ->findOrFail($request->product_id);

            $quantity = max(1, (int) $request->quantity);

            abort_if(
                ! $product->hasEnoughStock($quantity),
                404,
                'Stok tidak mencukupi.'
            );

            $items = collect([
                (object) [
                    'product'          => $product,
                    'quantity'         => $quantity,
                    'price'            => $product->price,
                    'subtotal'         => $product->price * $quantity,
                    'total_weight'     => $product->weight * $quantity,

                    // alias untuk view
                    'product_name'     => $product->name,
                    'product_image_url' => $product->thumbnail,
                    'product_price'    => $product->price,
                ],
            ]);

            $cart         = (object) ['items' => $items];
            $subtotal     = $items->sum('subtotal');
            $totalWeight  = $items->sum('total_weight');
            $checkoutMode = 'buy_now';

            /* ------------------------------------------------------------------ */
            /* CART NORMAL                                                          */
            /* ------------------------------------------------------------------ */
        } else {

            $cart = $user->cart()
                ->with(['items.product.primaryImage'])
                ->first();

            abort_if(
                ! $cart || $cart->items->isEmpty(),
                404,
                'Keranjang kosong.'
            );

            $subtotal     = $cart->items->sum('subtotal');
            $totalWeight  = $cart->items->sum('total_weight');
            $checkoutMode = 'cart';
        }

        /* ------------------------------------------------------------------ */
        /* Alamat user (aktif)                                                  */
        /* ------------------------------------------------------------------ */
        $addresses = $user->addresses()
            ->active()
            ->orderByDesc('is_default')
            ->get();

        /* ------------------------------------------------------------------ */
        /* Toko (pickup info + shipping origin untuk Biteship)                  */
        /* ------------------------------------------------------------------ */

        // Ambil toko yang allow pickup dan/atau shipping origin
        $store = Store::active()
            ->first(); // ambil toko pertama; kalau multi-store sesuaikan

        /* ------------------------------------------------------------------ */
        /* Payment methods (available)                                          */
        /* ------------------------------------------------------------------ */
        $paymentMethods = PaymentMethod::available()
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        /* ------------------------------------------------------------------ */
        /* Shipping methods (available) — dipakai sebagai fallback statis       */
        /* Nanti diganti response Biteship real-time via AJAX/HTMX              */
        /* ------------------------------------------------------------------ */
        $shippingMethods = ShippingMethod::available()
            ->orderBy('courier_name')
            ->orderBy('service_name')
            ->get();

        return view('checkout.index', compact(
            'cart',
            'addresses',
            'paymentMethods',
            'shippingMethods',
            'store',
            'subtotal',
            'totalWeight',
            'checkoutMode',
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | buyNow — Redirect ke checkout dengan mode buy_now
    |--------------------------------------------------------------------------
    */

    public function buyNow(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        return redirect()->route('checkout.index', [
            'mode'       => 'buy_now',
            'product_id' => $validated['product_id'],
            'quantity'   => $validated['quantity'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | store — Proses checkout, buat Order
    |--------------------------------------------------------------------------
    | Flow saat ini   : buat Order + OrderItem → redirect ke payment
    |
    | TODO Midtrans   : setelah Order dibuat, panggil Midtrans Snap API,
    |                   simpan snap_token ke Order, redirect ke Snap payment page
    |
    | TODO Biteship   : ongkir sudah dipilih di front-end (AJAX),
    |                   simpan shipping_method_id + rate_id ke Order
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_mode'      => ['required', 'in:delivery,pickup'],
            'payment_method_id'  => ['required', 'exists:payment_methods,id'],

            // hanya wajib kalau delivery
            'user_address_id'    => [
                'required_if:delivery_mode,delivery',
                'nullable',
                'exists:user_addresses,id',
            ],
            'shipping_option_id' => [
                'required_if:delivery_mode,delivery',
                'nullable',
                // TODO: ganti ke 'exists:shipping_methods,id' saat pakai Biteship
            ],

            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = auth()->user();

        /* ------------------------------------------------------------------ */
        /* Resolve cart items                                                   */
        /* ------------------------------------------------------------------ */
        if ($request->checkout_mode === 'buy_now') {

            // buy_now: ambil dari session atau re-query
            // (implement sesuai kebutuhan flow buy_now kamu)
            abort(501, 'Buy now store belum diimplementasikan.');
        } else {

            $cart = $user->cart()
                ->with(['items.product'])
                ->firstOrFail();

            abort_if($cart->items->isEmpty(), 400, 'Keranjang kosong.');
        }

        /* ------------------------------------------------------------------ */
        /* Hitung total                                                         */
        /* ------------------------------------------------------------------ */
        $subtotal     = $cart->items->sum('subtotal');
        $shippingFee  = 0;

        if ($validated['delivery_mode'] === 'delivery') {
            // TODO Biteship: ambil harga dari shipping_option_id
            // Sementara dummy — nanti diganti setelah integrasi Biteship
            $shippingFee = 0;
        }

        $total = $subtotal + $shippingFee;

        /* ------------------------------------------------------------------ */
        /* Resolve alamat pengiriman                                            */
        /* ------------------------------------------------------------------ */
        $shippingAddress = null;

        if ($validated['delivery_mode'] === 'delivery') {

            $shippingAddress = UserAddress::where('id', $validated['user_address_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
        }

        /* ------------------------------------------------------------------ */
        /* Buat Order                                                           */
        /* ------------------------------------------------------------------ */
        $order = Order::create([
            'user_id'            => $user->id,
            'order_number'       => $this->generateOrderNumber(),
            'status'             => 'pending_payment',

            // Delivery
            'delivery_mode'      => $validated['delivery_mode'],
            'user_address_id'    => $shippingAddress?->id,

            // Shipping
            // TODO Biteship: simpan shipping_method_id + biteship_order_id
            'shipping_method_id' => null,
            'shipping_fee'       => $shippingFee,

            // Payment
            'payment_method_id'  => $validated['payment_method_id'],
            'payment_status'     => 'unpaid',

            // Harga
            'subtotal'           => $subtotal,
            'total'              => $total,

            // Catatan
            'notes'              => $validated['notes'],
        ]);

        /* ------------------------------------------------------------------ */
        /* Buat Order Items                                                     */
        /* ------------------------------------------------------------------ */
        foreach ($cart->items as $item) {

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $item->price,
                'subtotal'   => $item->subtotal,
                'weight'     => $item->total_weight,
            ]);

            // Kurangi stok
            $item->product->decrement('stock', $item->quantity);
        }

        /* ------------------------------------------------------------------ */
        /* Kosongkan keranjang setelah checkout                                 */
        /* ------------------------------------------------------------------ */
        $cart->items()->delete();

        // /* ------------------------------------------------------------------ */
        // /* TODO Midtrans — Payment Gateway                                      */
        // |--------------------------------------------------------------------------
        // | Aktifkan blok ini setelah install midtrans/midtrans-php
        // |
        // | \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        // | \Midtrans\Config::$isProduction = config('midtrans.is_production');
        // | \Midtrans\Config::$isSanitized  = true;
        // | \Midtrans\Config::$is3ds        = true;
        // |
        // | $snapToken = \Midtrans\Snap::getSnapToken([
        // |     'transaction_details' => [
        // |         'order_id'     => $order->order_number,
        // |         'gross_amount' => $order->total,
        // |     ],
        // |     'customer_details' => [
        // |         'first_name' => $user->name,
        // |         'email'      => $user->email,
        // |         'phone'      => $user->phone,
        // |     ],
        // |     'item_details' => $cart->items->map(fn($i) => [
        // |         'id'       => $i->product_id,
        // |         'price'    => $i->price,
        // |         'quantity' => $i->quantity,
        // |         'name'     => $i->product->name,
        // |     ])->toArray(),
        // | ]);
        // |
        // | $order->update(['snap_token' => $snapToken]);
        // |
        // | return redirect()->route('payment.snap', $order->id);
        // |--------------------------------------------------------------------------
        // /* ------------------------------------------------------------------ */

        // /* ------------------------------------------------------------------ */
        // /* TODO Biteship — Buat Shipment Order                                  */
        // |--------------------------------------------------------------------------
        // | Aktifkan setelah integrasi Biteship:
        // |
        // | $biteshipOrder = app(\App\Services\BiteshipService::class)->createOrder([
        // |     'origin_contact_name'       => config('store.name'),
        // |     'origin_address'            => $store->full_address_text,
        // |     'origin_coord'              => [$store->latitude, $store->longitude],
        // |     'destination_contact_name'  => $shippingAddress->receiver_name,
        // |     'destination_contact_phone' => $shippingAddress->receiver_phone,
        // |     'destination_address'       => $shippingAddress->full_address_text,
        // |     'courier_company'           => $validated['shipping_option_id'],
        // |     'items'                     => ...,
        // | ]);
        // |
        // | $order->update(['biteship_order_id' => $biteshipOrder['id']]);
        // |--------------------------------------------------------------------------
        // /* ------------------------------------------------------------------ */

        return redirect()
            ->route('orders.show', $order->id)
            ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
    }

    /*
    |--------------------------------------------------------------------------
    | shippingRates — AJAX endpoint untuk fetch ongkir Biteship
    |--------------------------------------------------------------------------
    | Dipanggil via fetch/HTMX saat user memilih alamat di checkout.
    |
    | TODO: Implementasikan integrasi Biteship di sini.
    |--------------------------------------------------------------------------
    */

    public function shippingRates(Request $request)
    {
        $validated = $request->validate([
            'address_id' => ['required', 'exists:user_addresses,id'],
            'weight'     => ['required', 'integer', 'min:1'],
        ]);

        $address = UserAddress::where('id', $validated['address_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $store = Store::active()->shippingOrigin()->first()
            ?? Store::active()->first();

        /*
         * TODO Biteship: panggil Biteship Rates API
         *
         * $rates = app(\App\Services\BiteshipService::class)->getRates([
         *     'origin_latitude'       => $store->latitude,
         *     'origin_longitude'      => $store->longitude,
         *     'destination_latitude'  => $address->latitude,
         *     'destination_longitude' => $address->longitude,
         *     'items'                 => [
         *         ['name' => 'Produk', 'value' => 10000, 'weight' => $validated['weight']],
         *     ],
         * ]);
         *
         * return response()->json($rates['pricing']);
         */

        // Sementara kembalikan shipping methods dari DB
        $methods = ShippingMethod::available()
            ->orderBy('courier_name')
            ->get()
            ->map(fn($m) => [
                'id'           => $m->id,
                'name'         => $m->full_name,
                'courier_name' => $m->courier_name,
                'service_name' => $m->service_name,
                'etd'          => $m->estimated_delivery,
                'price'        => $m->additional_fee,
            ]);

        return response()->json([
            'rates' => $methods,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper — Generate order number
    |--------------------------------------------------------------------------
    */

    private function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(Str::random(8)) . '-' . now()->format('Ymd');
    }
}
