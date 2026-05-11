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

    /**
     * Initialize Midtrans config
     */
    public static function init(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    /**
     * Create payment via Midtrans Snap
     *
     * @param Order $order
     * @return array
     */
    public function createTransaction(Order $order): array
    {
        try {
            // Prepare transaction details
            $transactionDetails = [
                'order_id' => $order->invoice_number,
                'gross_amount' => (int) $order->grand_total,
            ];

            // Prepare customer details
            $customerDetails = [
                'first_name' => $order->user?->name ?? $order->customer_name,
                'email' => $order->user?->email ?? $order->customer_email,
                'phone' => $order->user?->phone ?? $order->customer_phone,
            ];

            // Prepare item details
            $itemDetails = $order->items->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'price' => (int) $item->price,
                    'quantity' => (int) $item->quantity,
                    'name' => $item->product?->name ?? 'Product',
                ];
            })->toArray();

            // Add shipping cost sebagai item jika ada
            if ($order->shipping_cost > 0) {
                $itemDetails[] = [
                    'id' => 'shipping',
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Ongkir ' . ($order->shipping_courier ?? 'Pengiriman'),
                ];
            }

            // Add service fee jika ada
            if ($order->service_fee > 0) {
                $itemDetails[] = [
                    'id' => 'service_fee',
                    'price' => (int) $order->service_fee,
                    'quantity' => 1,
                    'name' => 'Biaya Layanan',
                ];
            }

            // Add discount jika ada
            if ($order->discount_amount > 0) {
                $itemDetails[] = [
                    'id' => 'discount',
                    'price' => -((int) $order->discount_amount),
                    'quantity' => 1,
                    'name' => 'Diskon',
                ];
            }

            // Prepare Snap API payload
            $snapPayload = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
                'item_details' => $itemDetails,
                'callbacks' => [
                    'finish' => route('payment.finish', $order->id),
                    'unfinish' => route('payment.unfinish', $order->id),
                    'error' => route('payment.error', $order->id),
                ],
                'enabled_payments' => config('services.midtrans.enabled_payments', []),
            ];

            // Get Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($snapPayload);

            // Get Snap Redirect URL
            $snapRedirectUrl = \Midtrans\Snap::getSnapUrl($snapPayload);

            // Create/Update PaymentTransaction record
            $transaction = PaymentTransaction::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'gateway' => 'midtrans',
                    'gateway_order_id' => $order->invoice_number,
                    'payment_type' => 'snap',
                    'gross_amount' => (int) $order->grand_total,
                    'currency' => 'IDR',
                    'transaction_status' => 'pending',
                    'snap_token' => $snapToken,
                    'payment_url' => $snapRedirectUrl,
                    'expired_at' => now()->addHours(24),
                ]
            );

            Log::info('Midtrans transaction created', [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'amount' => $order->grand_total,
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'payment_url' => $snapRedirectUrl,
                'redirect_url' => $snapRedirectUrl,
                'transaction' => $transaction,
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans transaction error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle Midtrans notification (webhook)
     *
     * @param array $notification
     * @return array
     */
    public function handleNotification(array $notification): array
    {
        try {
            $orderId = $notification['order_id'];
            $transactionId = $notification['transaction_id'];
            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? null;

            // Find transaction
            $transaction = PaymentTransaction::where('gateway_order_id', $orderId)->first();

            if (!$transaction) {
                Log::warning('Midtrans notification - transaction not found', [
                    'order_id' => $orderId,
                    'transaction_id' => $transactionId,
                ]);

                return [
                    'success' => false,
                    'error' => 'Transaction not found',
                ];
            }

            // Update transaction record
            $transaction->update([
                'gateway_transaction_id' => $transactionId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payload' => $notification,
            ]);

            // Handle by status
            switch ($transactionStatus) {
                case 'capture':
                case 'settlement':
                    if ($fraudStatus === 'accept' || $fraudStatus === 'challenge') {
                        $transaction->markAsPaid($notification);
                    }
                    break;

                case 'pending':
                    // Already pending, no action needed
                    break;

                case 'deny':
                case 'cancel':
                case 'decline':
                case 'failed':
                    $transaction->markAsFailed($notification);
                    break;

                case 'expire':
                    $transaction->markAsExpired($notification);
                    break;
            }

            Log::info('Midtrans notification processed', [
                'order_id' => $orderId,
                'status' => $transactionStatus,
            ]);

            return [
                'success' => true,
                'transaction' => $transaction,
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans notification error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify Midtrans notification signature
     *
     * @param array $notification
     * @param string $signature
     * @return bool
     */
    public function verifyNotificationSignature(array $notification, string $signature): bool
    {
        $orderId = $notification['order_id'];
        $statusCode = $notification['status_code'];
        $grossAmount = $notification['gross_amount'];

        $serverKey = config('services.midtrans.server_key');

        $input = $orderId . $statusCode . $grossAmount . $serverKey;
        $hash = hash('sha512', $input);

        return $hash === $signature;
    }

    /**
     * Get transaction status from Midtrans
     *
     * @param string $transactionId
     * @return array
     */
    public function getTransactionStatus(string $transactionId): array
    {
        try {
            $status = \Midtrans\Transaction::status($transactionId);

            return [
                'success' => true,
                'data' => $status,
                'status' => $status->transaction_status,
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans get status error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel transaction di Midtrans
     *
     * @param string $transactionId
     * @return array
     */
    public function cancelTransaction(string $transactionId): array
    {
        try {
            \Midtrans\Transaction::cancel($transactionId);

            return [
                'success' => true,
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans cancel error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
