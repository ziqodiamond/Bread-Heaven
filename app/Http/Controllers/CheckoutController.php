<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\ShippingRate;
use App\Models\Store;
use App\Models\UserAddress;
use App\Models\Voucher;
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

            $product = Product::with('primaryImage', 'activeFlashSaleItem.flashSale')
                ->findOrFail($request->product_id);

            $quantity = max(1, (int) $request->quantity);

            abort_if(
                ! $product->hasEnoughStock($quantity),
                404,
                'Stok tidak mencukupi.'
            );

            $items = collect([
                (object) [
                    'product'           => $product,
                    'quantity'          => $quantity,
                    'price'             => $product->resolved_price,
                    'subtotal'          => $product->resolved_price * $quantity,
                    'total_weight'      => $product->weight * $quantity,

                    // alias untuk view
                    'product_name'      => $product->name,
                    'product_image_url' => $product->thumbnail,
                    'product_price'     => $product->resolved_price,
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
                ->with(['items.product.primaryImage', 'items.product.activeFlashSaleItem.flashSale'])
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

        // Get applied vouchers
        $appliedVouchers = [];
        if (isset($cart->vouchers) && is_array($cart->vouchers)) {
            $appliedVouchers = $cart->vouchers;
        }

        /* ------------------------------------------------------------------ */
        /* Alamat user (aktif)                                                  */
        /* ------------------------------------------------------------------ */
        $addresses = $user->addresses()
            ->active()
            ->orderByDesc('is_default')
            ->get();

        /* ------------------------------------------------------------------ */
        /* Toko — shipping origin untuk Biteship                               */
        /* ------------------------------------------------------------------ */
        $store = Store::active()
            ->shippingOrigin()
            ->first()
            ?? Store::active()->first();

        /* ------------------------------------------------------------------ */
        /* Payment methods (available)                                          */
        /* ------------------------------------------------------------------ */
        $paymentMethods = PaymentMethod::available()
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | $shippingMethods TIDAK lagi di-pass ke view untuk di-render statis.
        | Data ongkir diambil real-time via endpoint shippingRates() (fetch/HTMX).
        |--------------------------------------------------------------------------
        */

        $totalVoucherDiscount = ($cart->total_discount_amount ?? 0) + ($cart->total_shipping_discount ?? 0);

        return view('checkout.index', compact(
            'cart',
            'addresses',
            'paymentMethods',
            'store',
            'subtotal',
            'totalWeight',
            'checkoutMode',
            'appliedVouchers',
            'totalVoucherDiscount',
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

        // Defensive checks: ensure product still available
        $product = Product::find($validated['product_id']);

        if (! $product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        if (! $product->is_available) {
            return redirect()->back()->with('error', 'Produk tidak tersedia untuk dijual saat ini.');
        }

        if (! $product->hasEnoughStock($validated['quantity'])) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi untuk jumlah yang diminta.');
        }

        // Redirect ke checkout in buy_now mode
        return redirect()->route('checkout.index', [
            'mode'       => 'buy_now',
            'product_id' => $validated['product_id'],
            'quantity'   => $validated['quantity'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | store — Proses checkout
    |--------------------------------------------------------------------------
    |
    | FLOW YANG BENAR:
    |   1. Validasi input (shipping_method_id bukan shipping_rate_id)
    |   2. Verifikasi ShippingMethod ada & available
    |   3. Hitung ulang harga dari Biteship (pakai cache — tidak perlu hit API lagi)
    |   4. Buat Order
    |   5. Buat OrderItem
    |   6. ShippingRate::create() dengan order_id yang sudah ada  ← HANYA DI SINI
    |   7. Kosongkan cart
    |   8. Buat transaksi Midtrans
    |
    | PENTING: ShippingRate TIDAK dibuat di shippingRates() endpoint.
    |          ShippingRate adalah snapshot permanen setelah order confirmed.
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_mode'     => ['required', 'in:delivery,pickup'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],

            // Wajib saat delivery
            'user_address_id' => [
                'required_if:delivery_mode,delivery',
                'nullable',
                'exists:user_addresses,id',
            ],

            /*
            |----------------------------------------------------------------------
            | FIX: Ganti 'sometimes' + closure saja → tambah 'required_if' eksplisit.
            |
            | Masalah sebelumnya:
            |   - Alpine kirim shipping_method_id="" (string kosong) saat null
            |   - Laravel dengan 'sometimes' + 'nullable' bisa mengabaikan key tsb
            |     sehingga $validated['shipping_method_id'] tidak ada → ErrorException
            |
            | Solusi:
            |   - 'required_if:delivery_mode,delivery' → wajib ada & tidak kosong saat delivery
            |   - 'nullable' tetap ada → aman untuk mode pickup (nilai null/kosong diizinkan)
            |   - Closure tetap untuk validasi eksistensi ShippingMethod
            |----------------------------------------------------------------------
            */
            'shipping_method_id' => [
                'required_if:delivery_mode,delivery',
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    // Hanya validasi lebih lanjut jika ada nilainya
                    if (! empty($value)) {
                        $exists = ShippingMethod::where('id', $value)
                            ->where('status', 'available')
                            ->exists();

                        if (! $exists) {
                            $fail('Metode pengiriman tidak valid atau tidak tersedia.');
                        }
                    }
                },
            ],

            /*
            |----------------------------------------------------------------------
            | courier_code & service_code dikirim dari front-end
            | untuk verifikasi ulang harga ke Biteship (dari cache)
            |----------------------------------------------------------------------
            */
            'courier_code'  => ['nullable', 'string'],
            'service_code'  => ['nullable', 'string'],

            // Harga yang dipilih user — untuk cross-check
            'selected_price' => ['nullable', 'integer', 'min:0'],

            'notes'         => ['nullable', 'string', 'max:1000'],
            'checkout_mode' => ['nullable', 'string', 'in:cart,buy_now'],
        ]);

        $user = auth()->user();

        /* ------------------------------------------------------------------ */
        /* Resolve cart items                                                   */
        /* ------------------------------------------------------------------ */
        if ($request->checkout_mode === 'buy_now') {
            // TODO: implementasi buy_now store
            abort(501, 'Buy now store belum diimplementasikan.');
        }

        $cart = $user->cart()
            ->with(['items.product'])
            ->firstOrFail();

        abort_if($cart->items->isEmpty(), 400, 'Keranjang kosong.');

        /* ------------------------------------------------------------------ */
        /* Hitung subtotal & berat                                              */
        /* ------------------------------------------------------------------ */
        $subtotal    = $cart->items->sum('subtotal');
        $totalWeight = $cart->items->sum('total_weight');
        $shippingFee = 0;

        /*
        |--------------------------------------------------------------------------
        | Variabel untuk snapshot ShippingRate setelah order dibuat
        |--------------------------------------------------------------------------
        */
        $selectedShippingMethod = null;
        $resolvedCourierName    = '';
        $resolvedServiceName    = '';
        $resolvedEtd            = '';
        $resolvedCourierCode    = '';
        $resolvedServiceCode    = '';
        $shippingRateRaw        = [];
        $address                = null;
        $store                  = Store::active()->shippingOrigin()->first()
            ?? Store::active()->first();

        /* ------------------------------------------------------------------ */
        /* DELIVERY — Hitung ongkir                                             */
        /* ------------------------------------------------------------------ */
        if ($validated['delivery_mode'] === 'delivery') {

            // Verify alamat milik user
            $address = UserAddress::where('id', $validated['user_address_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();

            /*
            |----------------------------------------------------------------------
            | FIX: Ambil shipping_method_id dengan null-safe operator.
            |
            | Meskipun validasi 'required_if' di atas sudah menjamin key ada
            | saat delivery_mode=delivery, kita tambahkan guard eksplisit sebagai
            | defense in depth — mencegah ErrorException jika key entah bagaimana
            | tidak masuk ke $validated.
            |----------------------------------------------------------------------
            */
            $shippingMethodId = $validated['shipping_method_id'] ?? null;

            abort_if(
                empty($shippingMethodId),
                422,
                'Metode pengiriman wajib dipilih.'
            );

            $selectedShippingMethod = ShippingMethod::where('id', $shippingMethodId)
                ->where('status', 'available')
                ->firstOrFail();

            /*
            |----------------------------------------------------------------------
            | Hitung ulang harga dari Biteship (cache masih valid dari shippingRates)
            | Ini mencegah user manipulasi harga dari front-end
            |----------------------------------------------------------------------
            */
            if ($selectedShippingMethod->provider === 'biteship') {

                abort_if(
                    ! $store || ! $store->latitude || ! $store->longitude,
                    422,
                    'Konfigurasi toko belum lengkap.'
                );

                abort_if(
                    ! $address->latitude || ! $address->longitude,
                    422,
                    'Alamat belum memiliki koordinat GPS.'
                );

                $biteshipService = app(\App\Services\BiteshipService::class);

                $ratesResult = $biteshipService->getRates(
                    originLat: (float) $store->latitude,
                    originLng: (float) $store->longitude,
                    destLat: (float) $address->latitude,
                    destLng: (float) $address->longitude,
                    weight: $totalWeight,
                    items: []
                );

                abort_if(
                    ! $ratesResult['success'],
                    422,
                    'Gagal mengambil data ongkir: ' . ($ratesResult['error'] ?? 'Unknown error')
                );

                /*
                |------------------------------------------------------------------
                | Cari rate yang cocok berdasarkan courier_code + service_code
                |------------------------------------------------------------------
                */
                $matchedRate = collect($ratesResult['pricing'] ?? [])
                    ->first(function ($rate) use ($validated, $selectedShippingMethod) {
                        return $rate['courier_code'] === ($validated['courier_code'] ?? $selectedShippingMethod->courier_code)
                            && $rate['service_code'] === ($validated['service_code'] ?? $selectedShippingMethod->service_code);
                    });

                abort_if(
                    ! $matchedRate,
                    422,
                    'Layanan pengiriman yang dipilih tidak tersedia lagi. Silakan pilih ulang.'
                );

                /*
                |------------------------------------------------------------------
                | Final price = harga Biteship + additional_fee dari ShippingMethod
                |------------------------------------------------------------------
                */
                $additionalFee = $selectedShippingMethod->additional_fee ?? 0;
                $shippingFee   = (int) $matchedRate['price'] + $additionalFee;

                // Simpan untuk snapshot ShippingRate
                $resolvedCourierName = $matchedRate['courier_company'];
                $resolvedServiceName = $matchedRate['courier_type'];
                $resolvedEtd         = $matchedRate['etd'] ?? '';
                $resolvedCourierCode = $matchedRate['courier_code'];
                $resolvedServiceCode = $matchedRate['service_code'];
                $shippingRateRaw     = $matchedRate['raw'] ?? $matchedRate;
            } else {

                /*
                |------------------------------------------------------------------
                | Provider static (bukan Biteship) — pakai additional_fee
                |------------------------------------------------------------------
                */
                $shippingFee         = $selectedShippingMethod->additional_fee ?? 0;
                $resolvedCourierName = $selectedShippingMethod->courier_name;
                $resolvedServiceName = $selectedShippingMethod->service_name;
                $resolvedEtd         = $selectedShippingMethod->estimated_delivery ?? '';
                $resolvedCourierCode = $selectedShippingMethod->courier_code ?? '';
                $resolvedServiceCode = $selectedShippingMethod->service_code ?? '';
                $shippingRateRaw     = [];
            }
        }

        /* ------------------------------------------------------------------ */
        /* Payment fee & grand total                                            */
        /* ------------------------------------------------------------------ */
        $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);

        $serviceFee     = $paymentMethod->calculateFee($subtotal + $shippingFee);
        $discountAmount = 0;
        $shippingDiscount = 0;
        $appliedVouchers = [];

        // Handle multiple vouchers di cart
        if (!empty($cart->vouchers) && is_array($cart->vouchers)) {
            $voucherService = app(\App\Services\VoucherService::class);
            
            try {
                // Validasi ulang semua voucher saat user submit order
                foreach ($cart->vouchers as $voucherData) {
                    if (isset($voucherData['id'])) {
                        $voucher = \App\Models\Voucher::find($voucherData['id']);
                        
                        if ($voucher) {
                            // Re-validate voucher
                            $validationRules = $voucherService->validateQuotaAndRules($voucher, $cart, $user);
                            
                            if (!$validationRules['valid']) {
                                throw new \Exception($validationRules['message']);
                            }
                            
                            // Accumulate discounts
                            $discountAmount += $voucherData['discount_amount'] ?? 0;
                            $shippingDiscount += $voucherData['shipping_discount'] ?? 0;
                            $appliedVouchers[] = $voucherData;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Jika voucher tidak valid saat submit order → kembalikan user ke checkout
                session()->flash('checkout_alerts', [$e->getMessage()]);
                return redirect()->route('checkout.index');
            }
        }

        // Hitung final shipping cost & grand total
        $finalShippingCost = max(0, ($shippingFee - $shippingDiscount));
        $grandTotal     = $subtotal + $finalShippingCost + $serviceFee - $discountAmount;

        /* ------------------------------------------------------------------ */
        /* Verifikasi perubahan harga / stok sebelum buat order                */
        /* ------------------------------------------------------------------ */
        $submittedPrices = $request->input('selected_prices', []);
        $submittedQuantities = $request->input('selected_quantities', []);
        $alerts = [];
        $cartChanged = false;

        foreach ($cart->items as $item) {

            $fresh = Product::with('activeFlashSaleItem.flashSale')
                ->find($item->product_id);

            // Produk hilang dari sistem
            if (! $fresh) {
                $alerts[] = "Produk \"{$item->product_name}\" tidak tersedia lagi dan telah dihapus dari keranjang.";
                $item->delete();
                $cartChanged = true;
                continue;
            }

            // Cek perubahan harga sejak halaman checkout dibuka
            $submittedPrice = isset($submittedPrices[$item->product_id]) ? (int) $submittedPrices[$item->product_id] : $item->product_price;
            $currentPrice = $fresh->resolved_price;

            if ($submittedPrice !== $currentPrice) {
                $alerts[] = "Harga untuk \"{$fresh->name}\" berubah dari Rp " . number_format($submittedPrice, 0, ',', '.') . " menjadi Rp " . number_format($currentPrice, 0, ',', '.');
                $cartChanged = true;
            }

            // Cek stok
            $submittedQty = isset($submittedQuantities[$item->product_id]) ? (int) $submittedQuantities[$item->product_id] : $item->quantity;

            if (! $fresh->hasEnoughStock($submittedQty)) {
                if ($fresh->stock > 0) {
                    $item->update(['quantity' => $fresh->stock]);
                    $alerts[] = "Stok untuk \"{$fresh->name}\" berkurang menjadi {$fresh->stock}. Jumlah keranjang telah disesuaikan.";
                } else {
                    $item->delete();
                    $alerts[] = "Produk \"{$fresh->name}\" sudah habis dan telah dihapus dari keranjang.";
                }
                $cartChanged = true;
            }
        }

        if ($cartChanged) {
            // refresh cart dan kirim user kembali ke checkout dengan notifikasi
            $cart = $user->cart()->with(['items.product'])->first();
            session()->flash('checkout_alerts', $alerts);
            return redirect()->route('checkout.index');
        }

        /* ------------------------------------------------------------------ */
        /* Buat Order                                                           */
        /* ------------------------------------------------------------------ */
        $order = Order::create([
            'user_id'           => $user->id,
            'invoice_number'    => $this->generateInvoiceNumber(),
            'user_address_id'   => $address?->id,
            'payment_method_id' => $validated['payment_method_id'],

            // Informasi customer
            'customer_name'  => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,

            // Snapshot alamat pengiriman
            'shipping_receiver_name'  => $address?->receiver_name  ?? $user->name,
            'shipping_receiver_phone' => $address?->receiver_phone ?? ($user->phone ?? ''),
            'shipping_province'       => $address?->province       ?? '',
            'shipping_city'           => $address?->city           ?? '',
            'shipping_district'       => $address?->district       ?? '',
            'shipping_postal_code'    => $address?->postal_code    ?? '',
            'shipping_full_address'   => $address?->full_address   ?? ($store?->full_address ?? ''),
            'shipping_notes'          => $address?->notes          ?? '',

            // Shipping info
            'shipping_courier' => $resolvedCourierName ?: '',
            'shipping_service' => $resolvedServiceName ?: ($validated['delivery_mode'] === 'pickup' ? 'Ambil di Toko' : ''),
            'shipping_etd'     => $resolvedEtd,

            // Harga
            'subtotal'        => $subtotal,
            'shipping_cost'   => $shippingFee,
            'service_fee'     => $serviceFee,
            'discount_amount' => $discountAmount,
            'shipping_discount' => $shippingDiscount,
            'final_shipping_cost' => $finalShippingCost,
            'grand_total'     => $grandTotal,

            // Multiple vouchers snapshot
            'vouchers' => $appliedVouchers,
            'total_discount_amount' => $discountAmount,
            'total_shipping_discount' => $shippingDiscount,

            // Berat
            'total_weight' => $totalWeight,

            // Status
            'order_status'    => 'pending',
            'payment_status'  => 'unpaid',
            'payment_gateway' => 'midtrans',

            // Catatan
            'notes' => $validated['notes'] ?? null,
        ]);

        /* ------------------------------------------------------------------ */
        /* Buat Order Items + kurangi stok                                     */
        /* ------------------------------------------------------------------ */
        $voucherService = app(\App\Services\VoucherService::class);

        // Pre-calculate eligible products per voucher untuk akurat distribute discount
        $voucherEligibleMap = []; // voucher_id => [product_ids]
        $productEligibleSubtotal = []; // product_id => subtotal untuk eligible vouchers
        
        if (!empty($appliedVouchers)) {
            foreach ($appliedVouchers as $voucherData) {
                $voucherId = $voucherData['id'] ?? null;
                if (!$voucherId) continue;
                
                $voucher = Voucher::find($voucherId);
                if (!$voucher) continue;
                
                $eligibleProductIds = [];
                $eligibleSubtotal = 0;
                
                foreach ($cart->items as $item) {
                    if ($voucherService->isProductEligibleForVoucher($voucher, $item->product_id)) {
                        $eligibleProductIds[] = $item->product_id;
                        $eligibleSubtotal += $item->subtotal;
                    }
                }
                
                if (!empty($eligibleProductIds)) {
                    $voucherEligibleMap[$voucherId] = [
                        'product_ids' => $eligibleProductIds,
                        'subtotal' => $eligibleSubtotal,
                        'discount_amount' => $voucherData['discount_amount'] ?? 0,
                    ];
                }
            }
        }

        foreach ($cart->items as $item) {

            $product = $item->product;

            // Tentukan tipe diskon
            $discountSource = 'none';
            if ($product->is_flash_sale && $product->activeFlashSaleItem) {
                $discountSource = 'flash_sale';
            } elseif ($product->has_active_discount) {
                $discountSource = 'product_discount';
            }

            // Hitung diskon produk (flash sale / product discount)
            $originalPrice = $product->price;
            $finalPrice = $product->resolved_price;
            $discountPerItem = max(0, $originalPrice - $finalPrice);

            // Tentukan voucher applicable untuk produk ini dan hitung voucher discount
            $applicableVoucherIds = [];
            $totalVoucherDiscount = 0;

            foreach ($voucherEligibleMap as $voucherId => $eligibleData) {
                if (in_array($product->id, $eligibleData['product_ids'], true)) {
                    $applicableVoucherIds[] = $voucherId;
                    
                    // Proportional discount distribution
                    // discount_amount : eligible_subtotal = this_item_subtotal : X
                    $itemDiscount = (int) (($eligibleData['discount_amount'] * $item->subtotal) / max(1, $eligibleData['subtotal']));
                    $totalVoucherDiscount += $itemDiscount;
                }
            }

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,

                // Snapshot produk
                'product_name'        => $product->name,
                'product_slug'        => $product->slug,
                'product_sku'         => $product->sku,
                'product_description' => $product->description,
                'product_image_url'   => $product->thumbnail,

                // Snapshot harga — Prioritas: Flash Sale > Diskon > Harga Normal
                'original_price'      => $originalPrice,
                'product_price'       => $finalPrice,
                'discount_amount'     => $discountPerItem,
                'discount_percentage' => $originalPrice > 0 ? (int) round(($discountPerItem / $originalPrice) * 100) : 0,
                'discount_label'      => $product->discount_label ?? null,
                'discount_source'     => $discountSource,

                // Voucher info
                'voucher_ids' => !empty($applicableVoucherIds) ? $applicableVoucherIds : null,
                'voucher_discount_amount' => $totalVoucherDiscount,

                // Quantity
                'quantity' => $item->quantity,

                // Berat
                'product_weight' => $product->weight,
                'total_weight'   => $item->total_weight,

                // Total
                'original_subtotal' => $originalPrice * $item->quantity,
                'subtotal'          => $item->subtotal,

                // Status
                'status' => 'active',
            ]);

            // Kurangi stok
            $product->decreaseStock($item->quantity);
        }

        /*
        |--------------------------------------------------------------------------
        | Buat ShippingRate — HANYA setelah Order berhasil dibuat
        |--------------------------------------------------------------------------
        | Ini adalah snapshot permanen dari ongkir yang dipilih user.
        | Tidak dibuat di shippingRates() endpoint (yang hanya untuk preview).
        |--------------------------------------------------------------------------
        */
        if ($selectedShippingMethod && $validated['delivery_mode'] === 'delivery') {

            ShippingRate::create([
                'order_id' => $order->id,

                'provider'     => $selectedShippingMethod->provider,
                'courier_name' => $resolvedCourierName,
                'courier_code' => $resolvedCourierCode,
                'service_name' => $resolvedServiceName,
                'service_code' => $resolvedServiceCode,

                'weight' => $totalWeight,
                'etd'    => $resolvedEtd,
                'price'  => $shippingFee,

                // Snapshot koordinat & alamat
                'origin' => [
                    'latitude'  => $store?->latitude,
                    'longitude' => $store?->longitude,
                    'address'   => $store?->full_address ?? '',
                ],
                'destination' => [
                    'latitude'  => $address->latitude,
                    'longitude' => $address->longitude,
                    'address'   => $address->full_address_text ?? '',
                ],

                // Raw response dari Biteship (untuk audit/debug)
                'response' => $shippingRateRaw,
            ]);
        }

        /* ------------------------------------------------------------------ */
        /* Buat VoucherUsage (snapshot) untuk setiap voucher yang dipakai        */
        /* ------------------------------------------------------------------ */
        if (!empty($appliedVouchers) && is_array($appliedVouchers)) {
            try {
                $voucherService = app(\App\Services\VoucherService::class);
                
                foreach ($appliedVouchers as $voucherData) {
                    if (isset($voucherData['id'])) {
                        $voucher = \App\Models\Voucher::find($voucherData['id']);
                        
                        if ($voucher) {
                            $voucherService->applyVouchersToOrder([
                                'voucher' => $voucher,
                                'order' => $order,
                                'user' => $user,
                                'discount_amount' => $voucherData['discount_amount'] ?? 0,
                                'shipping_discount' => $voucherData['shipping_discount'] ?? 0,
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('CheckoutController::VoucherUsage failed', ['error' => $e->getMessage()]);
                // Tidak menghentikan alur checkout kalau perekaman voucher gagal
            }
        }

        /* ------------------------------------------------------------------ */
        /* Kosongkan keranjang                                                  */
        /* ------------------------------------------------------------------ */
        $cart->items()->delete();

        /* ------------------------------------------------------------------ */
        /* Buat transaksi Midtrans                                              */
        /* ------------------------------------------------------------------ */
        $midtransService = app(\App\Services\MidtransService::class);
        $paymentResult   = $midtransService->createTransaction($order);

        if (! $paymentResult['success']) {
            // Jika Midtrans gagal, cancel order
            $order->cancel();

            return redirect()
                ->back()
                ->with('error', 'Gagal membuat transaksi pembayaran: ' . $paymentResult['error']);
        }

        // Simpan payment reference
        $order->update([
            'payment_reference' => $paymentResult['transaction']->id,
        ]);

        return redirect($paymentResult['redirect_url'])
            ->with('success', 'Pesanan dibuat! Silakan lakukan pembayaran.');
    }

    /*
    |--------------------------------------------------------------------------
    | shippingRates — Endpoint untuk preview ongkir (fetch / HTMX)
    |--------------------------------------------------------------------------
    |
    | PENTING — Endpoint ini HANYA mengembalikan list ongkir untuk ditampilkan.
    |           TIDAK membuat ShippingRate di database.
    |
    | Flow:
    |   1. Ambil semua ShippingMethod available (semua provider)
    |   2. Untuk provider 'biteship': hit Biteship API (dengan cache)
    |      → filter hanya yang courier_code-nya ada di ShippingMethod available
    |   3. Untuk provider lain (static): pakai additional_fee dari ShippingMethod
    |   4. Return JSON gabungan — front-end tampilkan, user pilih
    |
    | Tidak ada write ke DB di sini.
    |--------------------------------------------------------------------------
    */

    public function shippingRates(Request $request)
    {
        $validated = $request->validate([
            'address_id' => ['required', 'exists:user_addresses,id'],
            'weight'     => ['required', 'integer', 'min:1'],
        ]);

        try {

            $user = auth()->user();

            /* -------------------------------------------------------------- */
            /* Verify address milik user                                        */
            /* -------------------------------------------------------------- */
            $address = UserAddress::where('id', $validated['address_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();

            /* -------------------------------------------------------------- */
            /* Ambil toko origin                                                */
            /* -------------------------------------------------------------- */
            $store = Store::active()
                ->shippingOrigin()
                ->first()
                ?? Store::active()->first();

            abort_if(
                ! $store || ! $store->latitude || ! $store->longitude,
                404,
                'Store tidak memiliki koordinat GPS.'
            );

            abort_if(
                ! $address->latitude || ! $address->longitude,
                400,
                'Alamat belum memiliki koordinat GPS.'
            );

            /* -------------------------------------------------------------- */
            /* Ambil semua ShippingMethod available                             */
            /* -------------------------------------------------------------- */
            $allMethods = ShippingMethod::where('status', 'available')
                ->orderBy('courier_name')
                ->orderBy('service_name')
                ->get();

            /*
            |------------------------------------------------------------------
            | Pisahkan: static (non-biteship) vs dynamic (biteship)
            |------------------------------------------------------------------
            */
            $staticMethods   = $allMethods->where('provider', '!=', 'biteship');
            $biteshipMethods = $allMethods->where('provider', 'biteship');

            /* -------------------------------------------------------------- */
            /* STATIC RATES — Langsung dari ShippingMethod                     */
            /* -------------------------------------------------------------- */
            $staticRates = $staticMethods->map(function ($method) {
                // Defensive: ensure $method is a ShippingMethod model
                if (! $method || ! is_object($method)) {
                    \Log::warning('CheckoutController::shippingRates - unexpected static shipping method type', ['method' => $method]);
                    return null;
                }

                return [
                    /*
                    |--------------------------------------------------------------
                    | id = ShippingMethod->id
                    | Ini yang dikirim ke form sebagai shipping_method_id
                    |--------------------------------------------------------------
                    */
                    'id'              => $method->id,
                    'provider'        => $method->provider,
                    'name'            => $method->courier_name . ' - ' . $method->service_name,
                    'courier_company' => $method->courier_name,
                    'courier_type'    => $method->service_name,
                    'courier_code'    => $method->courier_code ?? '',
                    'service_code'    => $method->service_code ?? '',
                    'service_type'    => 'static',
                    'description'     => $method->description,
                    'price'           => $method->additional_fee ?? 0,
                    'price_formatted' => 'Rp ' . number_format($method->additional_fee ?? 0, 0, ',', '.'),
                    'etd'             => $method->estimated_delivery,
                    'features'        => [],
                ];
            })->filter()->values();

            /* -------------------------------------------------------------- */
            /* DYNAMIC RATES — Dari Biteship API (cache)                       */
            /* -------------------------------------------------------------- */
            $dynamicRates = collect();

            if ($biteshipMethods->isNotEmpty()) {

                $biteshipService = app(\App\Services\BiteshipService::class);

                $ratesResult = $biteshipService->getRates(
                    originLat: (float) $store->latitude,
                    originLng: (float) $store->longitude,
                    destLat: (float) $address->latitude,
                    destLng: (float) $address->longitude,
                    weight: $validated['weight'],
                    items: []
                );

                if ($ratesResult['success']) {

                    /*
                    |----------------------------------------------------------
                    | Biteship mengembalikan SEMUA kurir yang bisa dipakai.
                    | Kita filter: hanya tampilkan yang ada di ShippingMethod
                    | dengan status 'available'.
                    |----------------------------------------------------------
                    */
                    $dynamicRates = collect($ratesResult['pricing'] ?? [])
                        ->map(function ($rate) use ($biteshipMethods) {

                            // Defensive: ensure rate shape
                            if (! is_array($rate)) {
                                \Log::warning('CheckoutController::shippingRates - unexpected rate shape', ['rate' => $rate]);
                                return null;
                            }

                            /*
                            |--------------------------------------------------
                            | Cari ShippingMethod yang cocok
                            | Cocok = courier_code + service_code sama
                            |--------------------------------------------------
                            */
                            $shippingMethod = $biteshipMethods->first(function ($m) use ($rate) {
                                // Defensive property access
                                $mCourier = is_object($m) ? ($m->courier_code ?? null) : null;
                                $mService = is_object($m) ? ($m->service_code ?? null) : null;

                                return $mCourier === ($rate['courier_code'] ?? null)
                                    && $mService === ($rate['service_code'] ?? null);
                            });

                            // Jika tidak ada di ShippingMethod → skip (eliminasi)
                            if (! $shippingMethod || ! is_object($shippingMethod)) {
                                \Log::warning('CheckoutController::shippingRates - shipping method not found for rate', ['rate' => $rate]);
                                return null;
                            }

                            $additionalFee = $shippingMethod->additional_fee ?? 0;
                            $finalPrice    = (int) ($rate['price'] ?? 0) + $additionalFee;

                            return [
                                /*
                                |----------------------------------------------
                                | id = ShippingMethod->id (bukan rate id Biteship)
                                | Konsisten: front-end selalu kirim shipping_method_id
                                |----------------------------------------------
                                */
                                'id'              => $shippingMethod->id,
                                'provider'        => 'biteship',
                                'name'            => ($rate['courier_company'] ?? '') . ' - ' . ($rate['courier_type'] ?? ''),
                                'courier_company' => $rate['courier_company'] ?? '',
                                'courier_type'    => $rate['courier_type'] ?? '',
                                'courier_code'    => $rate['courier_code'] ?? '',
                                'service_code'    => $rate['service_code'] ?? '',
                                'service_type'    => $rate['service_type'] ?? '',
                                'description'     => $rate['description'] ?? '',
                                'price'           => $finalPrice,
                                'price_formatted' => 'Rp ' . number_format($finalPrice, 0, ',', '.'),
                                'etd'             => $rate['etd'] ?? '',
                                'features'        => $rate['features'] ?? [],
                            ];
                        })
                        ->filter() // buang null
                        ->values();
                }
            }

            /* -------------------------------------------------------------- */
            /* Merge static + dynamic, sort by price                           */
            /* -------------------------------------------------------------- */
            // Ensure both collections are base Support collections before merging
            $rates = collect($staticRates)
                ->merge(collect($dynamicRates))
                ->sortBy('price')
                ->values();

            return response()->json([
                'success' => true,
                'rates'   => $rates,
            ]);
        } catch (\Exception $e) {

            \Log::error('CheckoutController::shippingRates error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'rates'   => [],
            ], 400);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper — Generate invoice number
    |--------------------------------------------------------------------------
    */

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . strtoupper(Str::random(8)) . '-' . now()->format('Ymd');
    }
}
