<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\VolunteerApplication;
use App\Models\VolunteerCampaign;
use Illuminate\Support\Facades\Auth;

class VolunteerApplicationController extends Controller
{
    /**
     * Get all volunteer applications for authenticated user
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $applications = VolunteerApplication::where('user_id', $user->id)
            ->with('campaign')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'message' => 'Volunteer applications retrieved successfully',
            'data' => $applications
        ]);
    }

    /**
     * Get specific volunteer application for authenticated user
     */
    public function show($id): JsonResponse
    {
        $user = Auth::user();
        $application = VolunteerApplication::where('id', $id)
            ->where('user_id', $user->id)
            ->with('campaign')
            ->first();

        if (!$application) {
            return response()->json([
                'message' => 'Volunteer application not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Volunteer application retrieved successfully',
            'data' => $application
        ]);
    }

    /**
     * Apply to volunteer campaign
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'volunteer_campaign_id' => 'required|exists:volunteer_campaigns,id',
            'motivation' => 'required|string',
            'skills' => 'required|string',
        ]);

        // Check if user has already applied to this campaign
        $existingApplication = VolunteerApplication::where('user_id', $user->id)
            ->where('volunteer_campaign_id', $request->volunteer_campaign_id)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'message' => 'You have already applied to this volunteer campaign'
            ], 409);
        }

        // Check if campaign still has available quota
        $campaign = VolunteerCampaign::find($request->volunteer_campaign_id);
        if ($campaign && $campaign->kuota_terisi >= $campaign->kuota_total) {
            return response()->json([
                'message' => 'This volunteer campaign has reached its maximum capacity'
            ], 400);
        }

        $application = VolunteerApplication::create([
            'user_id' => $user->id,
            'volunteer_campaign_id' => $request->volunteer_campaign_id,
            'motivation' => $request->motivation,
            'skills' => $request->skills,
            'status' => 'pending' // Default status
        ]);

        return response()->json([
            'message' => 'Volunteer application submitted successfully',
            'data' => $application
        ], 201);
    }

    /**
     * Update volunteer application (only allowed for pending applications)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = Auth::user();

        $application = VolunteerApplication::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$application) {
            return response()->json([
                'message' => 'Volunteer application not found'
            ], 404);
        }

        // Only allow updates for pending applications
        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Cannot update application that is not in pending status'
            ], 400);
        }

        $request->validate([
            'motivation' => 'required|string',
            'skills' => 'required|string',
        ]);

        $application->update([
            'motivation' => $request->motivation,
            'skills' => $request->skills,
        ]);

        return response()->json([
            'message' => 'Volunteer application updated successfully',
            'data' => $application
        ]);
    }

    /**
     * Cancel volunteer application (only allowed for pending applications)
     */
    public function destroy($id): JsonResponse
    {
        $user = Auth::user();

        $application = VolunteerApplication::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$application) {
            return response()->json([
                'message' => 'Volunteer application not found'
            ], 404);
        }

        // Only allow deletion for pending applications
        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Cannot cancel application that is not in pending status'
            ], 400);
        }

        $application->delete();

        return response()->json([
            'message' => 'Volunteer application cancelled successfully'
        ]);
    }
}