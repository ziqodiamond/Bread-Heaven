<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    /**
     * List payment
     */
    public function index(Request $request)
    {
        $payments = PaymentTransaction::query()

            ->with([
                'order',
            ])

            ->when($request->search, function ($query, $search) {

                return $query->where(
                    'gateway_order_id',
                    'like',
                    "%{$search}%"
                );
            })

            ->latest()

            ->paginate(10)

            ->withQueryString();

        return view(
            'admin.payment.index',
            compact('payments')
        );
    }

    /**
     * Detail payment
     */
    public function show(
        PaymentTransaction $paymentTransaction
    ) {
        $paymentTransaction->load([

            'order.user',
            'order.items',

        ]);

        return view(
            'admin.payment.show',
            compact('paymentTransaction')
        );
    }
}
