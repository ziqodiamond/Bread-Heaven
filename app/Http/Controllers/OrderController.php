<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(string $id)
    {
        $order = \App\Models\Order::with([

            /*
        |--------------------------------------------------------------------------
        | Relasi
        |--------------------------------------------------------------------------
        */

            'items',
            'paymentMethod',
            'paymentTransactions' => fn($q) => $q->latest(),

        ])->findOrFail($id);

        /*
    |--------------------------------------------------------------------------
    | Pastikan order milik user login
    |--------------------------------------------------------------------------
    */

        abort_if(
            $order->user_id !== auth()->id(),
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Ambil payment transaction terbaru
    |--------------------------------------------------------------------------
    */

        $transaction = $order
            ->paymentTransactions
            ->first();

        return view(
            'orders.show',
            compact(
                'order',
                'transaction'
            )
        );
    }

    public function history()
    {
        $orders = auth()
            ->user()
            ->orders()
            ->with([
                'items.product',
                'paymentTransactions',
            ])
            ->latest()
            ->paginate(10);

        return view(
            'orders.history',
            compact('orders')
        );
    }

    public function cancel(string $id)
    {
        $order = \App\Models\Order::with(
            'items.product'
        )->findOrFail($id);

        /*
    |--------------------------------------------------------------------------
    | Pastikan order milik user login
    |--------------------------------------------------------------------------
    */

        abort_if(
            $order->user_id !== auth()->id(),
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Validasi status
    |--------------------------------------------------------------------------
    */

        if (
            ! in_array(
                $order->payment_status,
                ['unpaid', 'pending']
            )
        ) {

            return back()->with(
                'error',
                'Pesanan tidak dapat dibatalkan'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Cancel transaction Midtrans
    |--------------------------------------------------------------------------
    */

        if ($order->payment_gateway === 'midtrans') {

            $response = app(
                \App\Services\MidtransService::class
            )->cancelTransaction(
                $order->invoice_number
            );

            if (! $response['success']) {

                return back()->with(
                    'error',
                    'Gagal membatalkan transaksi di Midtrans: '
                        . ($response['error'] ?? 'Unknown error')
                );
            }
        }
        /*
    |--------------------------------------------------------------------------
    | Update order
    |--------------------------------------------------------------------------
    */

        $order->update([

            'order_status' => 'cancelled',

            'payment_status' => 'failed',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Balikin stok
    |--------------------------------------------------------------------------
    */

        foreach ($order->items as $item) {

            $item->product?->increment(
                'stock',
                $item->quantity
            );
        }

        return back()->with(
            'success',
            'Pesanan berhasil dibatalkan'
        );
    }
}
