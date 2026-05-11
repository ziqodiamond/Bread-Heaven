<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Handle Midtrans notification (webhook)
     */
    public function notification(Request $request)
    {
        try {
            $midtransService = app(MidtransService::class);
            $result = $midtransService->handleNotification($request->all());

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification processed',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                ], 400);
            }

        } catch (\Exception $e) {
            \Log::error('PaymentController::notification error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Finish callback dari Midtrans (user redirect after payment success)
     */
    public function finish(Order $order)
    {
        // Check payment status di Midtrans
        if ($order->paymentTransactions()->exists()) {
            $transaction = $order->paymentTransactions()->latest()->first();

            $midtransService = app(MidtransService::class);
            $statusResult = $midtransService->getTransactionStatus(
                $transaction->gateway_transaction_id
            );

            if ($statusResult['success']) {
                $status = $statusResult['status'];

                if (in_array($status, ['settlement', 'capture', 'paid'])) {
                    return redirect()->route('orders.show', $order->id)
                        ->with('success', 'Pembayaran berhasil! Pesanan akan diproses.');
                } elseif ($status === 'pending') {
                    return redirect()->route('orders.show', $order->id)
                        ->with('info', 'Pembayaran dalam proses. Mohon tunggu.');
                }
            }
        }

        return redirect()->route('orders.show', $order->id);
    }

    /**
     * Unfinish callback dari Midtrans (payment incomplete)
     */
    public function unfinish(Order $order)
    {
        return redirect()->route('orders.show', $order->id)
            ->with('warning', 'Pembayaran belum selesai. Silakan coba lagi atau gunakan metode pembayaran lain.');
    }

    /**
     * Error callback dari Midtrans
     */
    public function error(Order $order)
    {
        return redirect()->route('orders.show', $order->id)
            ->with('error', 'Terjadi kesalahan pada pembayaran. Silakan coba lagi.');
    }

    /**
     * Show payment page/form untuk order
     * Optional - jika ingin custom payment page
     */
    public function show(Order $order)
    {
        // Ensure user is order owner
        $this->authorize('view', $order);

        // Get latest payment transaction
        $transaction = $order->paymentTransactions()->latest()->first();

        if (!$transaction) {
            abort(404, 'Transaction not found');
        }

        return view('payment.show', compact('order', 'transaction'));
    }

    /**
     * Retry payment untuk order yang failed
     */
    public function retry(Order $order)
    {
        // Ensure user is order owner
        $this->authorize('update', $order);

        // Check if payment already settled
        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'Pesanan sudah dibayar.');
        }

        // Create new Midtrans transaction
        $midtransService = app(MidtransService::class);
        $paymentResult = $midtransService->createTransaction($order);

        if (!$paymentResult['success']) {
            return redirect()->back()
                ->with('error', 'Gagal membuat transaksi: ' . $paymentResult['error']);
        }

        // Redirect ke Midtrans Snap
        return redirect($paymentResult['redirect_url']);
    }
}
