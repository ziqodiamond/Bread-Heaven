<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function index()
    {
        // Get today's date
        $today = Carbon::today();

        // Get today's transactions
        $transactionsToday = Transaction::whereDate('created_at', $today)->get();

        // Calculate total number of transactions
        $totalTransactions = $transactionsToday->count();

        // Calculate total items sold (sum of transaction details)
        $totalItemsSold = $transactionsToday->sum(function ($transaction) {
            return $transaction->details->sum('quantity');
        });

        // Calculate total revenue (sum of all transaction totals)
        $totalRevenue = $transactionsToday->sum('total_price');

        // Pass data to the view
        return view('admin.dashboard', [
            'totalTransactions' => $totalTransactions,
            'totalItemsSold' => $totalItemsSold,
            'totalRevenue' => $totalRevenue,
            'transactionsToday' => $transactionsToday,
        ]);
    }
}
