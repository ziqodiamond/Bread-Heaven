<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Shipment;
use App\Models\ShippingMethod;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    /**
     * List order
     */
    public function index(Request $request)
    {
        $statuses = [
            'pending',
            'processing',
            'shipped',
            'completed',
            'cancelled',
            'refunded',
        ];

        $orders = Order::query()
            ->with([
                'user',
                'paymentMethod',
                'shipments',
            ])
            ->when($request->status, function ($query, $status) {
                return $query->where('order_status', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {

                    $query->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact(
            'orders',
            'statuses'
        ));
    }

    /**
     * Detail order
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'address',
            'items.product',
            'paymentMethod',
            'paymentTransactions',
            'shipments',
        ]);

        // Get available shipping methods grouped by courier
        $shippingMethods = ShippingMethod::where('status', 'available')
            ->orderBy('courier_name')
            ->get();

        // Group by courier_name
        $couriers = [];
        foreach ($shippingMethods as $method) {
            $courierName = $method->courier_name;
            if (!isset($couriers[$courierName])) {
                $couriers[$courierName] = [
                    'code' => $method->courier_code ?? strtolower($courierName),
                    'name' => $courierName,
                    'services' => [],
                ];
            }
            $couriers[$courierName]['services'][] = [
                'name' => $method->service_name,
                'code' => $method->service_code,
            ];
        }

        // Reset array keys
        $couriers = array_values($couriers);

        return view('admin.orders.show', compact('order', 'couriers'));
    }

    /**
     * Process order
     */
    public function process(Order $order)
    {
        if ($order->payment_status !== 'paid') {

            return back()->with(
                'error',
                'Order belum dibayar.'
            );
        }

        $order->update([
            'order_status' => 'processing',
        ]);

        return back()->with(
            'success',
            'Order berhasil diproses.'
        );
    }

    /**
     * Form shipment
     */
    public function shipment(Order $order)
    {
        return view('admin.orders.shipment', compact('order'));
    }

    /**
     * Simpan shipment dan resi
     */
    public function storeShipment(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([

            'courier_name' => ['required'],
            'courier_service' => ['nullable'],
            'tracking_number' => ['required'],
            'notes' => ['nullable'],
        ]);

        DB::transaction(function () use (
            $validated,
            $order
        ) {

            $trackingUrl = $this->generateTrackingUrl(
                $validated['courier_name'],
                $validated['tracking_number']
            );

            Shipment::create([

                'order_id' => $order->id,

                'provider' => 'manual',

                'courier_name' => $validated['courier_name'],

                'courier_service' => $validated['courier_service'],

                'tracking_number' => $validated['tracking_number'],

                'tracking_url' => $trackingUrl,

                'status' => 'shipped',

                'notes' => $validated['notes'],

                'shipped_at' => now(),
            ]);

            $order->update([

                'tracking_number' => $validated['tracking_number'],

                'shipping_courier' => $validated['courier_name'],

                'shipping_service' => $validated['courier_service'],
            ]);

            $order->markAsShipped(
                $validated['tracking_number']
            );
        });

        return back()->with(
            'success',
            'Shipment berhasil dibuat.'
        );
    }

    /**
     * Mark delivered
     */
    public function delivered(Order $order)
    {
        $shipment = $order->shipments()->latest()->first();

        if (! $shipment) {

            return back()->with(
                'error',
                'Shipment tidak ditemukan.'
            );
        }

        $shipment->markAsDelivered();

        return back()->with(
            'success',
            'Order berhasil diselesaikan.'
        );
    }

    /**
     * Cancel order
     */
    public function cancel(
        Order $order,
        MidtransService $midtransService
    ) {
        if ($order->order_status === 'shipped') {

            return back()->with(
                'error',
                'Order yang sudah dikirim tidak bisa dicancel.'
            );
        }

        if ($order->payment_status === 'pending') {

            $midtransService->cancelTransaction(
                $order->invoice_number
            );
        }

        $order->cancel();

        return back()->with(
            'success',
            'Order berhasil dibatalkan.'
        );
    }

    /**
     * Refund order
     */
    public function refund(Order $order)
    {
        if ($order->order_status === 'shipped') {

            return back()->with(
                'error',
                'Order yang sudah dikirim tidak bisa direfund.'
            );
        }

        DB::transaction(function () use ($order) {

            foreach ($order->items as $item) {

                $item->product?->increaseStock(
                    $item->quantity
                );

                $item->refund();
            }

            $order->update([

                'order_status' => 'refunded',

                'payment_status' => 'refunded',
            ]);
        });

        return back()->with(
            'success',
            'Refund berhasil diproses.'
        );
    }

    /**
     * Mark order as paid manually (for testing/webhook failures)
     */
    public function markAsPaid(Order $order)
    {
        if ($order->payment_status === 'paid') {
            return back()->with(
                'error',
                'Order sudah dibayar.'
            );
        }

        DB::transaction(function () use ($order) {
            // Mark order as paid
            $order->markAsPaid();

            // Create payment transaction record for manual payment
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway' => 'manual',
                'gateway_transaction_id' => 'MANUAL-' . $order->invoice_number . '-' . now()->timestamp,
                'payment_type' => 'manual',
                'gross_amount' => $order->grand_total,
                'currency' => 'IDR',
                'transaction_status' => 'settlement',
                'payload' => [
                    'note' => 'Pembayaran manual oleh admin',
                ],
                'paid_at' => now(),
            ]);
        });

        return back()->with(
            'success',
            'Order berhasil ditandai sebagai dibayar.'
        );
    }

    /**
     * Generate tracking URL
     */
    private function generateTrackingUrl(
        string $courier,
        string $resi
    ): ?string {
        $courier = strtolower($courier);

        return match ($courier) {

            'jne' => 'https://www.jne.co.id/id/tracking/trace',

            'j&t', 'jnt' => 'https://jet.co.id/track',

            'sicepat' => 'https://www.sicepat.com/checkAwb',

            'anteraja' => 'https://anteraja.id/tracking',

            default => null,
        };
    }

    /**
     * Mark order as complete
     */
    public function complete(Order $order)
    {
        if ($order->order_status !== 'shipped') {

            return back()->with(
                'error',
                'Order harus dalam status terkirim untuk diselesaikan.'
            );
        }

        $order->markAsCompleted();

        return back()->with(
            'success',
            'Order berhasil diselesaikan.'
        );
    }
}
