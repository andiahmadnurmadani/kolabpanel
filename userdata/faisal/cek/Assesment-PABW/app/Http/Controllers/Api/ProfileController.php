<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Get authenticated user's profile
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $responseData = [
            'message' => 'User profile retrieved successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'coins' => $user->coins ?? 0,
                'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
                'phone' => $user->phone,
                'birth_date' => $user->birth_date,
                'created_at' => $user->created_at->toISOString(),
                'updated_at' => $user->updated_at->toISOString(),
            ]
        ];

        $response = response()->json($responseData);

        \Log::info('Profile show response:', ['response' => $response->getContent()]);

        return $response;
    }

    /**
     * Update authenticated user's profile
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        // Determine if updating profile or password
        if ($request->has('password')) {
            // Update password
            $request->validate([
                'current_password' => 'required|current_password',
                'password' => 'required|min:8|confirmed',
            ]);

            $user->password = Hash::make($request->password);
            $user->save();

            $response = response()->json([
                'message' => 'Password updated successfully'
            ]);

            \Log::info('Password update response:', ['response' => $response->getContent()]);

            return $response;
        } else {
            // Update profile
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:15',
                'birth_date' => 'nullable|date',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->birth_date = $request->birth_date;

            // Handle profile photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($user->photo && Storage::exists('public/' . $user->photo)) {
                    Storage::delete('public/' . $user->photo);
                }

                $photoPath = $request->file('photo')->store('profile-photos', 'public');
                $user->photo = $photoPath;
            }

            $user->save();

            $responseData = [
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'coins' => $user->coins ?? 0,
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
                    'phone' => $user->phone,
                    'birth_date' => $user->birth_date,
                    'updated_at' => $user->updated_at->toISOString(),
                ]
            ];

            $response = response()->json($responseData);

            \Log::info('Profile update response:', ['response' => $response->getContent()]);

            return $response;
        }
    }

    /**
     * Get user's donation transaction history
     */
    public function getDonationHistory(Request $request): JsonResponse
    {
        $user = $request->user();

        $donationTransactions = \App\Models\DonationTransaction::where('user_id', $user->id)
            ->with(['campaign'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'message' => 'Donation history retrieved successfully',
            'data' => $donationTransactions
        ]);
    }

    /**
     * Get user's volunteer application history
     */
    public function getVolunteerHistory(Request $request): JsonResponse
    {
        $user = $request->user();

        $volunteerApplications = \App\Models\VolunteerApplication::where('user_id', $user->id)
            ->with(['campaign'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'message' => 'Volunteer history retrieved successfully',
            'data' => $volunteerApplications
        ]);
    }

    /**
     * Get user's complete profile history (donations + volunteer)
     */
    public function getCompleteHistory(Request $request): JsonResponse
    {
        $user = $request->user();

        $donationTransactions = \App\Models\DonationTransaction::where('user_id', $user->id)
            ->with(['campaign'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $volunteerApplications = \App\Models\VolunteerApplication::where('user_id', $user->id)
            ->with(['campaign'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Combine and sort by created_at
        $allActivities = collect();
        $allActivities = $allActivities->concat($donationTransactions->map(function($item) {
            return [
                'type' => 'donation',
                'id' => $item->id,
                'order_id' => $item->order_id,
                'amount' => $item->amount ?? null,
                'status' => $item->status,
                'title' => $item->campaign ? $item->campaign->title : 'Donasi Umum',
                'created_at' => $item->created_at,
                'status_label' => $item->status_label
            ];
        }))->concat($volunteerApplications->map(function($item) {
            return [
                'type' => 'volunteer',
                'id' => $item->id,
                'order_id' => $item->id, // Using ID as order_id for volunteer applications
                'amount' => null,
                'status' => $item->status,
                'title' => $item->campaign ? $item->campaign->title : 'Pendaftaran Relawan',
                'created_at' => $item->created_at,
                'status_label' => ucfirst($item->status)
            ];
        }))->sortByDesc('created_at')->values();

        return response()->json([
            'message' => 'Complete user history retrieved successfully',
            'data' => [
                'donation_transactions' => $donationTransactions,
                'volunteer_applications' => $volunteerApplications,
                'all_activities' => $allActivities
            ]
        ]);
    }

    /**
     * Get user statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        $user = $request->user();

        // Calculate donation statistics
        $totalDonationAmount = \App\Models\DonationTransaction::where('user_id', $user->id)
            ->whereIn('status', ['VERIFIED']) // Only verified donations
            ->sum('amount');

        $totalDonations = \App\Models\DonationTransaction::where('user_id', $user->id)
            ->count();

        $successfulDonations = \App\Models\DonationTransaction::where('user_id', $user->id)
            ->whereIn('status', ['VERIFIED'])
            ->count();

        // Calculate volunteer statistics
        $totalVolunteerApps = \App\Models\VolunteerApplication::where('user_id', $user->id)
            ->count();

        $acceptedVolunteerApps = \App\Models\VolunteerApplication::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->count();

        return response()->json([
            'message' => 'User statistics retrieved successfully',
            'data' => [
                'total_donation_amount' => $totalDonationAmount,
                'total_donations' => $totalDonations,
                'successful_donations' => $successfulDonations,
                'total_volunteer_applications' => $totalVolunteerApps,
                'accepted_volunteer_applications' => $acceptedVolunteerApps,
                'total_coins' => $user->coins ?? 0,
                'total_activities' => $totalDonations + $totalVolunteerApps
            ]
        ]);
    }
}