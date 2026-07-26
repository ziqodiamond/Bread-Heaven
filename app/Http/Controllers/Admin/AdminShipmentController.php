<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;

class AdminShipmentController extends Controller
{
    /**
     * List shipment
     */
    public function index(Request $request)
    {
        $shipments = Shipment::query()

            ->with([
                'order',
            ])

            ->when($request->search, function ($query, $search) {

                return $query->where(
                    'tracking_number',
                    'like',
                    "%{$search}%"
                );
            })

            ->latest()

            ->paginate(10)

            ->withQueryString();

        return view(
            'admin.shipment.index',
            compact('shipments')
        );
    }

    /**
     * Detail shipment
     */
    public function show(Shipment $shipment)
    {
        $shipment->load([

            'order.user',
            'order.items',

        ]);

        return view(
            'admin.shipment.show',
            compact('shipment')
        );
    }

    /**
     * Mark delivered
     */
    public function delivered(
        Shipment $shipment
    ) {
        $shipment->markAsDelivered();

        return back()->with(
            'success',
            'Shipment berhasil diselesaikan.'
        );
    }

    /**
     * Update tracking number dan mark as shipped
     */
    public function updateTrackingNumber(
        Shipment $shipment,
        Request $request
    ) {
        $request->validate([
            'tracking_number' => 'required|string|min:3',
        ], [
            'tracking_number.required' => 'Nomor resi harus diisi',
            'tracking_number.min' => 'Nomor resi minimal 3 karakter',
        ]);

        $shipment->markAsShipped(
            $request->input('tracking_number')
        );

        return back()->with(
            'success',
            'Resi berhasil disimpan dan order status diubah menjadi shipped.'
        );
    }

    /**
     * Cancel shipment
     */
    public function cancel(
        Shipment $shipment
    ) {
        $shipment->cancel();

        return back()->with(
            'success',
            'Shipment berhasil dibatalkan.'
        );
    }
}
