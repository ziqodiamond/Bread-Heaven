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
            'shipping_rate_id' => [
                'required_if:delivery_mode,delivery',
                'nullable',
                'exists:shipping_rates,id',
            ],

            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = auth()->user();

        /* ------------------------------------------------------------------ */
        /* Resolve cart items                                                   */
        /* ------------------------------------------------------------------ */
        if ($request->checkout_mode === 'buy_now') {
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
        $totalWeight  = $cart->items->sum('total_weight');
        $shippingFee  = 0;
        $shippingRate = null;

        if ($validated['delivery_mode'] === 'delivery') {
            // Fetch shipping rate yang dipilih
            $shippingRate = \App\Models\ShippingRate::findOrFail(
                $validated['shipping_rate_id']
            );

            // Verify alamat belongs to user
            $address = UserAddress::where('id', $validated['user_address_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();

            $shippingFee = $shippingRate->price;
        } else {
            // Pickup - no shipping fee
            $address = null;
        }

        $paymentMethod = \App\Models\PaymentMethod::findOrFail(
            $validated['payment_method_id']
        );

        $serviceFee = $paymentMethod->calculateFee(
            $subtotal + $shippingFee
        );
        $discountAmount = 0;
        $grandTotal = $subtotal + $shippingFee + $serviceFee - $discountAmount;

        /* ------------------------------------------------------------------ */
        /* Buat Order                                                           */
        /* ------------------------------------------------------------------ */
        $store = Store::active()->shippingOrigin()->first()
            ?? Store::active()->first();

        $order = Order::create([
            'user_id'            => $user->id,
            'invoice_number'     => $this->generateInvoiceNumber(),
            'user_address_id'    => $address?->id,
            'payment_method_id'  => $validated['payment_method_id'],

            // Informasi Customer
            'customer_name'      => $user->name,
            'customer_email'     => $user->email,
            'customer_phone'     => $user->phone,

            // Snapshot Alamat Pengiriman
            'shipping_receiver_name'   => $address?->receiver_name,
            'shipping_receiver_phone'  => $address?->receiver_phone,
            'shipping_province'        => $address?->province,
            'shipping_city'            => $address?->city,
            'shipping_district'        => $address?->district,
            'shipping_postal_code'     => $address?->postal_code,
            'shipping_full_address'    => $address?->full_address,
            'shipping_notes'           => $address?->notes,

            // Shipping Info
            'shipping_courier'   => $shippingRate?->courier_name,
            'shipping_service'   => $shippingRate?->service_name,
            'shipping_etd'       => $shippingRate?->etd,

            // Harga
            'subtotal'           => $subtotal,
            'shipping_cost'      => $shippingFee,
            'service_fee'        => $serviceFee,
            'discount_amount'    => $discountAmount,
            'grand_total'        => $grandTotal,

            // Berat
            'total_weight'       => $totalWeight,

            // Status
            'order_status'       => 'pending',
            'payment_status'     => 'unpaid',
            'payment_gateway'    => 'midtrans',

            // Catatan
            'notes'              => $validated['notes'],
        ]);

        /* ------------------------------------------------------------------ */
        /* Buat Order Items                                                     */
        /* ------------------------------------------------------------------ */
        foreach ($cart->items as $item) {

            $product = $item->product;

            \App\Models\OrderItem::create([

                /*
        |--------------------------------------------------------------------------
        | Relasi
        |--------------------------------------------------------------------------
        */

                'order_id'   => $order->id,
                'product_id' => $product->id,

                /*
        |--------------------------------------------------------------------------
        | Snapshot Produk
        |--------------------------------------------------------------------------
        */

                'product_name'        => $product->name,
                'product_slug'        => $product->slug,
                'product_sku'         => $product->sku,
                'product_description' => $product->description,
                'product_image_url'   => $product->thumbnail,

                /*
        |--------------------------------------------------------------------------
        | Snapshot Harga
        |--------------------------------------------------------------------------
        */

                'product_price' => $product->price,

                /*
        |--------------------------------------------------------------------------
        | Quantity
        |--------------------------------------------------------------------------
        */

                'quantity' => $item->quantity,

                /*
        |--------------------------------------------------------------------------
        | Berat
        |--------------------------------------------------------------------------
        */

                'product_weight' => $product->weight,
                'total_weight'   => $item->total_weight,

                /*
        |--------------------------------------------------------------------------
        | Total Harga
        |--------------------------------------------------------------------------
        */

                'subtotal' => $item->subtotal,

                /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

                'status' => 'active',
            ]);

            /*
    |--------------------------------------------------------------------------
    | Kurangi stok produk
    |--------------------------------------------------------------------------
    */

            $product->decreaseStock($item->quantity);
        }

        /* ------------------------------------------------------------------ */
        /* Snapshot Shipping Rate ke ShippingRate jika delivery                 */
        /* ------------------------------------------------------------------ */
        if ($shippingRate) {
            $shippingRate->update([
                'order_id' => $order->id,
                'origin' => [
                    'latitude' => $store->latitude,
                    'longitude' => $store->longitude,
                    'address' => $store->full_address,
                ],
                'destination' => [
                    'latitude' => $address->latitude,
                    'longitude' => $address->longitude,
                    'address' => $address->full_address_text,
                ],
            ]);
        }

        /* ------------------------------------------------------------------ */
        /* Kosongkan keranjang setelah checkout                                 */
        /* ------------------------------------------------------------------ */
        $cart->items()->delete();

        /* ------------------------------------------------------------------ */
        /* Create Midtrans Transaction (Payment)                               */
        /* ------------------------------------------------------------------ */
        $midtransService = app(\App\Services\MidtransService::class);
        $paymentResult = $midtransService->createTransaction($order);

        if (!$paymentResult['success']) {
            // Jika Midtrans gagal, cancel order
            $order->cancel();
            return redirect()
                ->back()
                ->with('error', 'Gagal membuat transaksi pembayaran: ' . $paymentResult['error']);
        }

        // Save payment reference
        $order->update([
            'payment_reference' => $paymentResult['transaction']->id,
        ]);

        /* ------------------------------------------------------------------ */
        /* Redirect ke Midtrans Snap Payment Page                              */
        /* ------------------------------------------------------------------ */
        return redirect($paymentResult['redirect_url'])
            ->with('success', 'Pesanan dibuat! Silakan lakukan pembayaran.');
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
            'weight'     => ['required', 'integer', 'min:100'],
        ]);

        try {

            $user = auth()->user();

            /*
        |--------------------------------------------------------------------------
        | Verify address milik user
        |--------------------------------------------------------------------------
        */

            $address = UserAddress::where(
                'id',
                $validated['address_id']
            )
                ->where('user_id', $user->id)
                ->firstOrFail();

            /*
        |--------------------------------------------------------------------------
        | Ambil toko origin
        |--------------------------------------------------------------------------
        */

            $store = Store::active()
                ->shippingOrigin()
                ->first()

                ?? Store::active()->first();

            abort_if(
                !$store ||
                    !$store->latitude ||
                    !$store->longitude,
                404,
                'Store tidak memiliki koordinat GPS'
            );

            abort_if(
                !$address->latitude ||
                    !$address->longitude,
                400,
                'Alamat belum memiliki koordinat GPS'
            );

            /*
        |--------------------------------------------------------------------------
        | STATIC PROVIDERS
        |--------------------------------------------------------------------------
        | Semua provider selain biteship
        |--------------------------------------------------------------------------
        */

            $staticRates = ShippingMethod::query()

                ->where('provider', '!=', 'biteship')

                ->where('status', 'available')

                ->get()

                ->map(function ($method) {

                    return [

                        'id' => $method->id,

                        'provider' => $method->provider,

                        'name' =>
                        $method->courier_name .
                            ' - ' .
                            $method->service_name,

                        'courier_company' =>
                        $method->courier_name,

                        'courier_type' =>
                        $method->service_name,

                        'service_type' => 'static',

                        'description' =>
                        $method->description,

                        'price' =>
                        $method->additional_fee,

                        'price_formatted' =>
                        'Rp ' .
                            number_format(
                                $method->additional_fee,
                                0,
                                ',',
                                '.'
                            ),

                        'etd' =>
                        $method->estimated_delivery,

                        'features' => [],
                    ];
                });

            /*
        |--------------------------------------------------------------------------
        | CEK BITESHIP AKTIF
        |--------------------------------------------------------------------------
        */

            $biteshipExists = ShippingMethod::query()

                ->where('provider', 'biteship')

                ->where('status', 'available')

                ->exists();

            /*
        |--------------------------------------------------------------------------
        | Dynamic rates default kosong
        |--------------------------------------------------------------------------
        */

            $dynamicRates = [];

            /*
        |--------------------------------------------------------------------------
        | FETCH BITESHIP
        |--------------------------------------------------------------------------
        */

            if ($biteshipExists) {

                $biteshipService = app(
                    \App\Services\BiteshipService::class
                );

                $ratesResult = $biteshipService->getRates(
                    originLat: (float) $store->latitude,
                    originLng: (float) $store->longitude,
                    destLat: (float) $address->latitude,
                    destLng: (float) $address->longitude,
                    weight: $validated['weight'],
                    items: []
                );

                /*
            |--------------------------------------------------------------------------
            | Jika request berhasil
            |--------------------------------------------------------------------------
            */

                if ($ratesResult['success']) {

                    $dynamicRates = array_map(
                        function ($rate) use (
                            $address,
                            $store,
                            $validated
                        ) {

                            /*
                        |--------------------------------------------------------------------------
                        | Save shipping rate
                        |--------------------------------------------------------------------------
                        */

                            $shippingRate =
                                \App\Models\ShippingRate::create([

                                    'provider' => 'biteship',

                                    'courier_name' =>
                                    $rate['courier_company'],

                                    'courier_code' =>
                                    $rate['courier_company'],

                                    'service_name' =>
                                    $rate['courier_type'],

                                    'service_code' =>
                                    $rate['courier_type'],

                                    'origin' => [
                                        'latitude' =>
                                        $store->latitude,

                                        'longitude' =>
                                        $store->longitude,
                                    ],

                                    'destination' => [
                                        'latitude' =>
                                        $address->latitude,

                                        'longitude' =>
                                        $address->longitude,
                                    ],

                                    'weight' =>
                                    $validated['weight'],

                                    'etd' =>
                                    $rate['etd'],

                                    'price' =>
                                    $rate['price'],

                                    'response' => $rate,
                                ]);

                            /*
                        |--------------------------------------------------------------------------
                        | Format response
                        |--------------------------------------------------------------------------
                        */

                            return [

                                'id' => $shippingRate->id,

                                'provider' => 'biteship',

                                'name' =>
                                $rate['courier_company'] .
                                    ' - ' .
                                    $rate['courier_type'],

                                'courier_company' =>
                                $rate['courier_company'],

                                'courier_type' =>
                                $rate['courier_type'],

                                'service_type' =>
                                $rate['service_type'],

                                'description' =>
                                $rate['description'] ?? '',

                                'price' =>
                                $rate['price'],

                                'price_formatted' =>
                                'Rp ' .
                                    number_format(
                                        $rate['price'],
                                        0,
                                        ',',
                                        '.'
                                    ),

                                'etd' =>
                                $rate['etd'],

                                'features' =>
                                $rate['features'] ?? [],
                            ];
                        },
                        $ratesResult['pricing'] ?? []
                    );
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Merge static + dynamic rates
        |--------------------------------------------------------------------------
        */

            $rates = [

                ...$staticRates->toArray(),

                ...$dynamicRates,
            ];

            /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

            return response()->json([

                'success' => true,

                'rates' => $rates,
            ]);
        } catch (\Exception $e) {

            \Log::error(
                'CheckoutController::shippingRates error',
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );

            return response()->json([

                'success' => false,

                'error' => $e->getMessage(),

                'rates' => [],
            ], 400);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper — Generate order number
    |--------------------------------------------------------------------------
    */

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . strtoupper(Str::random(8)) . '-' . now()->format('Ymd');
    }
}
