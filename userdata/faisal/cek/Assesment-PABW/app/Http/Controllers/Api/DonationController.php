<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonationTransaction;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{


    /**
     * Get user's donation history
     */
    public function getUserDonations(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $donations = DonationTransaction::where('user_id', $user->id)
            ->with('campaign')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'message' => 'User donations retrieved successfully',
            'data' => $donations
        ]);
    }

    /**
     * Get donation detail
     */
    public function show(Request $request, string $order_id): JsonResponse
    {
        $user = $request->user();
        
        $transaction = DonationTransaction::where('order_id', $order_id)
            ->where('user_id', $user->id)
            ->with('campaign')
            ->first();

        if (!$transaction) {
            return response()->json([
                'message' => 'Donation not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Donation retrieved successfully',
            'data' => $transaction
        ]);
    }

    /**
     * Get all donations (admin only)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Check if user is admin
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        $donations = DonationTransaction::with(['user', 'campaign'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'message' => 'Donations retrieved successfully',
            'data' => $donations
        ]);
    }

    /**
     * Update donation status (admin only)
     */
    public function updateStatus(Request $request, string $order_id): JsonResponse
    {
        $user = $request->user();
        
        // Check if user is admin
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:AWAITING_TRANSFER,PENDING_VERIFICATION,VERIFIED,CANCELLED'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $transaction = DonationTransaction::where('order_id', $order_id)->first();

        if (!$transaction) {
            return response()->json([
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
            'VERIFIED' => 'Payment successfully verified. Donation processed and kindness points added.',
            'CANCELLED' => 'Payment rejected. Donation cancelled.',
            'PENDING_VERIFICATION' => 'Payment status changed to pending verification.',
            'AWAITING_TRANSFER' => 'Payment status changed to awaiting transfer.'
        ];

        $message = $statusMessages[$request->status] ?? 'Transaction status updated successfully';

        return response()->json([
            'message' => $message,
            'data' => $transaction
        ]);
    }

    /**
     * Get donation detail for admin (with proof image)
     */
    public function showForAdmin(Request $request, string $order_id): JsonResponse
    {
        $user = $request->user();

        // Check if user is admin
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        $transaction = DonationTransaction::where('order_id', $order_id)
            ->with(['user', 'campaign'])
            ->first();

        if (!$transaction) {
            return response()->json([
                'message' => 'Donation not found'
            ], 404);
        }

        // Add proof image URL if proof exists
        if ($transaction->proof_of_transfer_path) {
            $transaction->proof_url = asset('storage/' . $transaction->proof_of_transfer_path);
        }

        return response()->json([
            'message' => 'Donation retrieved successfully',
            'data' => $transaction
        ]);
    }
}