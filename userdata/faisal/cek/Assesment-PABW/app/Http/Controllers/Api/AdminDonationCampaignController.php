<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminDonationCampaignController extends Controller
{
    /**
     * Get all donation campaigns for admin
     */
    public function index(): JsonResponse
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $campaigns = Campaign::with('user')->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'message' => 'Donation campaigns retrieved successfully',
            'data' => $campaigns
        ]);
    }

    /**
     * Get specific donation campaign for admin
     */
    public function show($id): JsonResponse
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $campaign = Campaign::with('user')->find($id);

        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Donation campaign retrieved successfully',
            'data' => $campaign
        ]);
    }

    /**
     * Create new donation campaign for admin
     */
    public function store(Request $request): JsonResponse
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:0',
            'end_date' => 'required|date|after_or_equal:today',
            'kategori' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:Active,Inactive,Completed',
            'user_id' => 'required|exists:users,id'
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('campaigns', 'public');
            $data['image'] = $imagePath;
        }

        $data['slug'] = Str::slug($request->title);
        
        // Check if slug already exists and make unique
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Campaign::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $campaign = Campaign::create($data);

        return response()->json([
            'message' => 'Donation campaign created successfully',
            'data' => $campaign
        ], 201);
    }

    /**
     * Update donation campaign for admin
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found'
            ], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:0',
            'end_date' => 'required|date|after_or_equal:today',
            'kategori' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:Active,Inactive,Completed',
            'user_id' => 'required|exists:users,id'
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($campaign->image && Storage::disk('public')->delete($campaign->image)) {
                // Old image deleted
            }

            $imagePath = $request->file('image')->store('campaigns', 'public');
            $data['image'] = $imagePath;
        }

        // Update slug if title changed
        if (isset($data['title']) && $data['title'] !== $campaign->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $counter = 1;

            // Check if slug already exists (excluding current campaign)
            while (Campaign::where('slug', $slug)->where('id', '!=', $campaign->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $data['slug'] = $slug;
        }

        $campaign->update($data);

        return response()->json([
            'message' => 'Donation campaign updated successfully',
            'data' => $campaign
        ]);
    }

    /**
     * Delete donation campaign for admin
     */
    public function destroy($id): JsonResponse
    {
        // Only allow admin users
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found'
            ], 404);
        }

        // Delete image if exists
        if ($campaign->image) {
            Storage::disk('public')->delete($campaign->image);
        }

        $campaign->delete();

        return response()->json([
            'message' => 'Donation campaign deleted successfully'
        ]);
    }
}