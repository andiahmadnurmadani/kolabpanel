<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationVerificationController extends Controller
{
    /**
     * Display the API verification page
     */
    public function index()
    {
        $this->checkAdmin();
        return view('admin.donations.api-verification');
    }
    /**
     * Check if user is admin
     */
    private function checkAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Access denied. You must be an admin to access this page.');
        }
    }

    /**
     * Get donation transaction with proof details
     */
    public function getTransactionWithProof($order_id)
    {
        $this->checkAdmin();

        $transaction = DonationTransaction::where('order_id', $order_id)
            ->with(['user', 'campaign'])
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        // Prepare response with proof details
        $response = [
            'success' => true,
            'data' => [
                'id' => $transaction->id,
                'order_id' => $transaction->order_id,
                'amount' => $transaction->amount,
                'donor_name' => $transaction->donor_name,
                'donor_email' => $transaction->donor_email,
                'donor_phone' => $transaction->donor_phone,
                'status' => $transaction->status,
                'status_label' => $transaction->status_label,
                'payment_method' => $transaction->payment_method,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                'proof_of_transfer_path' => $transaction->proof_of_transfer_path,
                'proof_url' => $transaction->proof_of_transfer_path ? asset('storage/' . $transaction->proof_of_transfer_path) : null,
                'campaign' => $transaction->campaign ? [
                    'id' => $transaction->campaign->id,
                    'title' => $transaction->campaign->title,
                    'description' => $transaction->campaign->description,
                ] : null,
                'transfer_deadline' => $transaction->transfer_deadline ? $transaction->transfer_deadline->format('Y-m-d H:i:s') : null,
                'bank_account_name' => $transaction->bank_account_name,
                'bank_account_number' => $transaction->bank_account_number,
                'bank_name' => $transaction->bank_name,
            ]
        ];

        return response()->json($response);
    }

    /**
     * Verify donation transaction
     */
    public function verifyTransaction(Request $request, $order_id)
    {
        $this->checkAdmin();

        $request->validate([
            'status' => 'required|in:VERIFIED,CANCELLED,PENDING_VERIFICATION,AWAITING_TRANSFER'
        ]);

        $transaction = DonationTransaction::where('order_id', $order_id)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        $oldStatus = $transaction->status;
        $transaction->update(['status' => $request->status]);

        // If status is changing to VERIFIED (Paid) from a non-verified status, update campaign amount
        if ($request->status === 'VERIFIED' && $oldStatus !== 'VERIFIED' && $transaction->campaign_id) {
            // Add the donation amount to the campaign's current amount
            $campaign = \App\Models\Campaign::find($transaction->campaign_id);
            if ($campaign) {
                $campaign->increment('current_amount', $transaction->amount);
            }
        }
        // If status is changing from VERIFIED to something else, subtract the donation amount
        elseif ($oldStatus === 'VERIFIED' && $request->status !== 'VERIFIED' && $transaction->campaign_id) {
            $campaign = \App\Models\Campaign::find($transaction->campaign_id);
            if ($campaign) {
                $campaign->decrement('current_amount', $transaction->amount);
            }
        }

        // Award coins if transaction is marked as VERIFIED (Paid) and was previously not verified
        if ($request->status === 'VERIFIED' && $oldStatus !== 'VERIFIED' && $transaction->user) {
            $transaction->user->addCoins(1, 'donation_completed');
        }

        // Determine success message based on status
        $statusMessages = [
            'VERIFIED' => 'Pembayaran berhasil diverifikasi. Donasi telah diproses dan poin kebaikan ditambahkan.',
            'CANCELLED' => 'Pembayaran ditolak. Donasi dibatalkan.',
            'PENDING_VERIFICATION' => 'Status pembayaran diubah menjadi menunggu verifikasi.',
            'AWAITING_TRANSFER' => 'Status pembayaran diubah menjadi menunggu transfer.'
        ];

        $message = $statusMessages[$request->status] ?? 'Status transaksi berhasil diperbarui';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'order_id' => $transaction->order_id,
                'status' => $transaction->status,
                'status_label' => $transaction->status_label
            ]
        ]);
    }

    /**
     * Get all pending verification transactions
     */
    public function getPendingVerifications()
    {
        $this->checkAdmin();

        $transactions = DonationTransaction::where('status', 'PENDING_VERIFICATION')
            ->with(['campaign'])
            ->orderBy('created_at', 'desc')
            ->get();

        $result = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'order_id' => $transaction->order_id,
                'amount' => $transaction->amount,
                'donor_name' => $transaction->donor_name,
                'donor_email' => $transaction->donor_email,
                'status' => $transaction->status,
                'status_label' => $transaction->status_label,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                'proof_of_transfer_path' => $transaction->proof_of_transfer_path,
                'proof_url' => $transaction->proof_of_transfer_path ? asset('storage/' . $transaction->proof_of_transfer_path) : null,
                'campaign' => $transaction->campaign ? [
                    'id' => $transaction->campaign->id,
                    'title' => $transaction->campaign->title,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}