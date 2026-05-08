<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class TransactionController extends Controller
{

    public function index(Request $request)
    {
        // Dapatkan semua filter dari request
        $status = $request->get('status');
        $paymentStatus = $request->get('payment_status');
        $search = $request->get('search');

        // Query awal untuk mengambil data transaksi
        $query = Transaction::query();

        // Filter berdasarkan status jika ada
        if ($status) {
            $query->where('status', $status);
        }

        // Filter berdasarkan payment_status jika ada
        if ($paymentStatus) {
            $query->where('payment_status', $paymentStatus);
        }

        // Jika ada search keyword, filter transaksi berdasarkan nama user atau email
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            });
        }

        // Ambil semua transaksi setelah query difilter
        $transactions = $query->with('user')->paginate(10);
        // Return the admin transaction view with the transactions data
        return view('admin.transaction', compact('transactions'));
    }

    // app/Http/Controllers/TransactionController.php

    public function destroy($id)
    {
        // Temukan transaksi berdasarkan ID
        $transaction = Transaction::findOrFail($id);

        // Hapus transaksi
        $transaction->delete();

        // Kembalikan respons JSON
        return redirect()->back()->with('success', 'Transaction deleted successfully');
    }





    public function show($transactionId)
    {
        $transaction = Transaction::with('details.product', 'paymentMethod', 'deliveryMethod')
            ->findOrFail($transactionId);

        // Get the associated payment method
        $paymentMethod = PaymentMethod::findOrFail($transaction->payment_method);

        return view('history-transaction', compact('transaction', 'paymentMethod'));
    }

    public function afterCheckout($transactionId)
    {
        // Redirect to the transaction details page after checkout
        return redirect()->route('transaction.show', $transactionId);
    }

    public function history()
    {
        $transactions = Transaction::where('user_id', auth()->id())->paginate(10);
        return view('mutasi-transaction', compact('transactions'));
    }

    public function cancel($id)
    {
        $transaction = Transaction::find($id);
        $transaction->status = 'canceled';
        $transaction->save();

        return redirect()->back()->with('status', 'Transaction canceled successfully.');
    }

    public function accept($id)
    {
        $transaction = Transaction::find($id);
        $transaction->status = 'process';
        $transaction->payment_status = 'paid';
        $transaction->save();

        return redirect()->back()->with('status', 'Payment accepted and transaction is now in process.');
    }

    public function ship($id)
    {
        $transaction = Transaction::find($id);
        $transaction->status = 'shipped';
        $transaction->save();

        return redirect()->back()->with('status', 'Transaction has been marked as shipped.');
    }

    public function complete($id)
    {
        $transaction = Transaction::find($id);
        $transaction->status = 'completed';
        $transaction->save();

        return redirect()->back()->with('status', 'Transaction has been completed.');
    }
}
