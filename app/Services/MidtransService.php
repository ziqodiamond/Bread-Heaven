<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;

class MidtransService
{
    public function __construct()
    {
        $this->init();
    }

    /*
    |--------------------------------------------------------------------------
    | Initialize Midtrans Config
    |--------------------------------------------------------------------------
    */

    private function init(): void
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$clientKey    = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Create Transaction (Snap)
    |--------------------------------------------------------------------------
    */

    /**
     * Buat transaksi baru via Midtrans Snap
     */
    public function createTransaction(Order $order): array
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Transaction Details
            |--------------------------------------------------------------------------
            */

            $transactionDetails = [
                'order_id'     => $order->invoice_number,
                'gross_amount' => (int) $order->grand_total,
            ];

            /*
            |--------------------------------------------------------------------------
            | Customer Details
            |--------------------------------------------------------------------------
            */

            $customerDetails = [
                'first_name' => $order->user?->name ?? $order->customer_name,
                'email'      => $order->user?->email ?? $order->customer_email,
                'phone'      => $order->user?->phone ?? $order->customer_phone,

                'shipping_address' => [
                    'first_name'  => $order->shipping_receiver_name,
                    'phone'       => $order->shipping_receiver_phone,
                    'address'     => $order->shipping_full_address,
                    'city'        => $order->shipping_city,
                    'postal_code' => $order->shipping_postal_code,
                ],
            ];

            /*
            |--------------------------------------------------------------------------
            | Enabled Payments (dari payment method)
            |--------------------------------------------------------------------------
            */

            $enabledPayments = [];

            $paymentMethod = $order->paymentMethod;

            if ($paymentMethod?->provider === 'midtrans' && $paymentMethod?->code) {
                $enabledPayments = array_map(
                    'trim',
                    explode(',', $paymentMethod->code)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Item Details
            |--------------------------------------------------------------------------
            */

            $itemDetails = $order->items->map(function ($item) {
                return [
                    'id'       => $item->product_id,
                    'price'    => (int) $item->product_price,
                    'quantity' => (int) $item->quantity,
                    'name'     => substr($item->product_name, 0, 50), // max 50 char Midtrans
                ];
            })->toArray();

            // Tambah ongkir sebagai item jika ada
            if ($order->shipping_cost > 0) {
                $itemDetails[] = [
                    'id'       => 'shipping',
                    'price'    => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name'     => 'Ongkir ' . ($order->shipping_courier ?? 'Pengiriman'),
                ];
            }

            // Tambah service fee jika ada
            if ($order->service_fee > 0) {
                $itemDetails[] = [
                    'id'       => 'service_fee',
                    'price'    => (int) $order->service_fee,
                    'quantity' => 1,
                    'name'     => 'Biaya Layanan',
                ];
            }

            // Tambah diskon jika ada (harga negatif)
            if ($order->discount_amount > 0) {
                $itemDetails[] = [
                    'id'       => 'discount',
                    'price'    => - ((int) $order->discount_amount),
                    'quantity' => 1,
                    'name'     => 'Diskon',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Snap Payload
            |--------------------------------------------------------------------------
            */

            $snapPayload = [
                'transaction_details' => $transactionDetails,
                'customer_details'    => $customerDetails,
                'item_details'        => $itemDetails,
                'callbacks'           => [
                    'finish'   => route('payment.finish', $order->id),
                    'unfinish' => route('payment.unfinish', $order->id),
                    'error'    => route('payment.error', $order->id),
                ],
            ];

            // Hanya set enabled_payments jika ada isinya
            if (!empty($enabledPayments)) {
                $snapPayload['enabled_payments'] = $enabledPayments;
            }

            /*
            |--------------------------------------------------------------------------
            | Get Snap Token & URL
            |--------------------------------------------------------------------------
            */

            $snapToken       = \Midtrans\Snap::getSnapToken($snapPayload);
            $snapRedirectUrl = \Midtrans\Snap::getSnapUrl($snapPayload);

            /*
            |--------------------------------------------------------------------------
            | Simpan / Update PaymentTransaction
            |--------------------------------------------------------------------------
            */

            $transaction = PaymentTransaction::updateOrCreate(
                [
                    'order_id' => $order->id,
                ],
                [
                    'gateway'              => 'midtrans',
                    'gateway_order_id'     => $order->invoice_number,
                    'payment_type'         => 'snap',
                    'gross_amount'         => (int) $order->grand_total,
                    'currency'             => 'IDR',
                    'transaction_status'   => 'pending',
                    'snap_token'           => $snapToken,
                    'payment_url'          => $snapRedirectUrl,
                    'expired_at'           => now()->addHours(24),
                ]
            );

            Log::info('Midtrans transaction created', [
                'order_id'       => $order->id,
                'invoice_number' => $order->invoice_number,
                'amount'         => $order->grand_total,
            ]);

            return [
                'success'      => true,
                'snap_token'   => $snapToken,
                'payment_url'  => $snapRedirectUrl,
                'redirect_url' => $snapRedirectUrl,
                'transaction'  => $transaction,
            ];
        } catch (\Exception $e) {

            Log::error('Midtrans createTransaction error', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Handle Notification (Webhook)
    |--------------------------------------------------------------------------
    */

    /**
     * Proses notifikasi webhook dari Midtrans
     * Midtrans\Notification otomatis verifikasi signature key
     */
    public function handleNotification(array $notification): array
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Auto-verify signature via Midtrans\Notification
            |--------------------------------------------------------------------------
            */

            $notif = new \Midtrans\Notification();

            $orderId           = $notif->order_id;
            $transactionId     = $notif->transaction_id;
            $transactionStatus = $notif->transaction_status;
            $fraudStatus       = $notif->fraud_status ?? null;

            /*
            |--------------------------------------------------------------------------
            | Cari transaksi di DB
            |--------------------------------------------------------------------------
            */

            $transaction = PaymentTransaction::where(
                'gateway_order_id',
                $orderId
            )->first();

            if (! $transaction) {

                Log::warning('Midtrans webhook - transaction not found', [
                    'order_id'       => $orderId,
                    'transaction_id' => $transactionId,
                ]);

                return [
                    'success' => false,
                    'error'   => 'Transaction not found',
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Update data transaksi
            |--------------------------------------------------------------------------
            */

            $transaction->update([
                'gateway_transaction_id' => $transactionId,
                'transaction_status'     => $transactionStatus,
                'fraud_status'           => $fraudStatus,
                'payload'                => (array) $notif,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Handle berdasarkan status
            |--------------------------------------------------------------------------
            */

            switch ($transactionStatus) {

                case 'capture':
                case 'settlement':
                    /*
                     * Hanya proses jika fraud_status = accept atau null
                     * challenge = perlu review manual, jangan langsung paid
                     */
                    if ($fraudStatus === 'accept' || $fraudStatus === null) {
                        $transaction->markAsPaid((array) $notif);
                    }
                    break;

                case 'pending':
                    // Tidak perlu action, sudah pending di DB
                    break;

                case 'deny':
                case 'cancel':
                case 'decline':
                case 'failed':
                    $transaction->markAsFailed((array) $notif);
                    break;

                case 'expire':
                    $transaction->markAsExpired((array) $notif);
                    break;
            }

            Log::info('Midtrans webhook processed', [
                'order_id' => $orderId,
                'status'   => $transactionStatus,
            ]);

            return [
                'success'     => true,
                'transaction' => $transaction,
            ];
        } catch (\Exception $e) {

            Log::error('Midtrans handleNotification error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get Transaction Status
    |--------------------------------------------------------------------------
    */

    /**
     * Cek status transaksi langsung dari Midtrans
     */
    public function getTransactionStatus(string $transactionId): array
    {
        try {

            $status = \Midtrans\Transaction::status($transactionId);

            return [
                'success' => true,
                'data'    => $status,
                'status'  => $status->transaction_status,
            ];
        } catch (\Exception $e) {

            Log::error('Midtrans getTransactionStatus error', [
                'transaction_id' => $transactionId,
                'error'          => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Cancel Transaction
    |--------------------------------------------------------------------------
    */

    /**
     * Batalkan transaksi di Midtrans
     */
    public function cancelTransaction(string $transactionId): array
    {
        try {

            \Midtrans\Transaction::cancel($transactionId);

            Log::info('Midtrans transaction cancelled', [
                'transaction_id' => $transactionId,
            ]);

            return [
                'success' => true,
            ];
        } catch (\Exception $e) {

            Log::error('Midtrans cancelTransaction error', [
                'transaction_id' => $transactionId,
                'error'          => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Verify Notification Signature (Manual)
    |--------------------------------------------------------------------------
    */

    /**
     * Verifikasi signature hash notifikasi Midtrans secara manual
     * Gunakan ini jika tidak pakai Midtrans\Notification
     */
    public function verifySignature(array $notification): bool
    {
        $orderId     = $notification['order_id'];
        $statusCode  = $notification['status_code'];
        $grossAmount = $notification['gross_amount'];
        $serverKey   = config('services.midtrans.server_key');

        $hash = hash(
            'sha512',
            $orderId . $statusCode . $grossAmount . $serverKey
        );

        return $hash === ($notification['signature_key'] ?? '');
    }
}
