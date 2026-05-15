<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Handle Midtrans notification (webhook) — tanpa auth middleware
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
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 400);
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
     * Finish callback dari Midtrans — redirect ke halaman sukses
     */
    public function finish(Order $order)
    {
        // Pastikan order milik user login
        abort_if($order->user_id !== auth()->id(), 403);

        // Cek status terkini dari Midtrans
        if ($order->paymentTransactions()->exists()) {
            $transaction = $order->paymentTransactions()->latest()->first();

            $midtransService = app(MidtransService::class);
            $statusResult = $midtransService->getTransactionStatus(
                $transaction->gateway_order_id
            );

            if ($statusResult['success']) {
                $status = $statusResult['status'];

                // Jika sukses → halaman sukses
                if (in_array($status, ['settlement', 'capture', 'paid'])) {
                    return redirect()->route('payment.success', $order->id);
                }

                // Jika pending → halaman order dengan info
                if ($status === 'pending') {
                    return redirect()->route('orders.show', $order->id)
                        ->with('info', 'Pembayaran dalam proses. Mohon tunggu konfirmasi.');
                }
            }
        }

        // Fallback — cek dari DB saja
        if ($order->payment_status === 'paid') {
            return redirect()->route('payment.success', $order->id);
        }

        return redirect()->route('orders.show', $order->id)
            ->with('info', 'Pembayaran sedang diverifikasi.');
    }

    /**
     * Halaman sukses pembayaran
     */
    public function success(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load([
            'items',
            'paymentMethod',
            'paymentTransactions' => fn($q) => $q->latest(),
        ]);

        $transaction = $order->paymentTransactions->first();

        return view('payment.success', compact('order', 'transaction'));
    }

    /**
     * Unfinish callback — user tutup popup sebelum selesai
     */
    public function unfinish(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        return redirect()->route('orders.show', $order->id)
            ->with('warning', 'Pembayaran belum selesai. Silakan lanjutkan pembayaran.');
    }

    /**
     * Error callback dari Midtrans
     */
    public function error(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        return redirect()->route('orders.show', $order->id)
            ->with('error', 'Terjadi kesalahan pada pembayaran. Silakan coba lagi.');
    }

    /**
     * Show payment page untuk order
     */
    public function show(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $transaction = $order->paymentTransactions()->latest()->first();

        if (!$transaction) {
            abort(404, 'Transaction not found');
        }

        return view('payment.show', compact('order', 'transaction'));
    }

    /**
     * Retry payment untuk order yang failed/expired
     */
    public function retry(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'Pesanan sudah dibayar.');
        }

        $midtransService = app(MidtransService::class);
        $paymentResult = $midtransService->createTransaction($order);

        if (!$paymentResult['success']) {
            return redirect()->back()
                ->with('error', 'Gagal membuat transaksi: ' . $paymentResult['error']);
        }

        return redirect($paymentResult['redirect_url']);
    }
}
