<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CampaignController extends Controller
{
    /**
     * Display a listing of campaigns with pagination.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        try {
            \Log::info('API V1 Campaign Index called', ['params' => $request->all()]);

            $query = Campaign::with('user');

            // Add filters if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('kategori')) {
                $query->where('kategori', $request->kategori);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Sort by end_date ascending (nearest first) by default
            $query->orderBy('end_date', 'asc');

            $campaigns = $query->paginate(10);

            \Log::info('Campaigns count: ' . $campaigns->count());

            return CampaignResource::collection($campaigns);
        } catch (\Exception $e) {
            \Log::error('Error in API V1 Campaign Index: ' . $e->getMessage());
            \Log::error('Error trace: ' . $e->getTraceAsString());

            throw $e;
        }
    }

    /**
     * Store a newly created campaign in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_amount' => 'required|numeric|min:0',
            'end_date' => 'required|date|after_or_equal:today',
            'kategori' => 'required|string|in:Lingkungan,Kesehatan,Pendidikan,Sosial Kemanusiaan,Bencana Alam',
            'yayasan' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|string|in:Active,Inactive,Completed',
        ]);

        // Handle image upload if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('campaigns', 'public');
        }

        $validatedData['image'] = $imagePath;
        $validatedData['user_id'] = $request->user()->id; // Assuming user is authenticated
        $validatedData['slug'] = Str::slug($request->title);

        // Check if slug already exists and make unique
        $originalSlug = $validatedData['slug'];
        $counter = 1;
        while (Campaign::where('slug', $validatedData['slug'])->exists()) {
            $validatedData['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $campaign = Campaign::create($validatedData);

        return response()->json([
            'message' => 'Campaign created successfully',
            'data' => new CampaignResource($campaign->load('user'))
        ], 201);
    }

    /**
     * Display the specified campaign by slug or ID.
     */
    public function show(string $id): CampaignResource|JsonResponse
    {
        $campaign = Campaign::with('user')->where('slug', $id)->orWhere('id', $id)->first();

        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found'
            ], 404);
        }

        return new CampaignResource($campaign);
    }

    /**
     * Update the specified campaign in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $campaign = Campaign::where('slug', $id)->orWhere('id', $id)->first();

        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'target_amount' => 'sometimes|required|numeric|min:0',
            'end_date' => 'sometimes|required|date|after_or_equal:today',
            'kategori' => 'sometimes|required|string|in:Lingkungan,Kesehatan,Pendidikan,Sosial Kemanusiaan,Bencana Alam',
            'yayasan' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'sometimes|string|in:Active,Inactive,Completed',
        ]);

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($campaign->image) {
                Storage::disk('public')->delete($campaign->image);
            }

            $imagePath = $request->file('image')->store('campaigns', 'public');
            $validatedData['image'] = $imagePath;
        }

        // Update slug if title changed
        if (isset($validatedData['title']) && $validatedData['title'] !== $campaign->title) {
            $slug = Str::slug($validatedData['title']);
            $originalSlug = $slug;
            $counter = 1;
            
            // Check if slug already exists (excluding current campaign)
            while (Campaign::where('slug', $slug)->where('id', '!=', $campaign->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            $validatedData['slug'] = $slug;
        }

        $campaign->update($validatedData);

        return response()->json([
            'message' => 'Campaign updated successfully',
            'data' => new CampaignResource($campaign->load('user'))
        ]);
    }

    /**
     * Remove the specified campaign from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $campaign = Campaign::where('slug', $id)->orWhere('id', $id)->first();

        if (!$campaign) {
            return response()->json([
                'message' => 'Campaign not found'
            ], 404);
        }

        // Delete associated image if exists
        if ($campaign->image) {
            Storage::disk('public')->delete($campaign->image);
        }

        $campaign->delete();

        return response()->json([
            'message' => 'Campaign deleted successfully'
        ], 200);
    }

    /**
     * Get urgent campaigns (campaigns with less than 3 days remaining and at least 50% funded)
     */
    public function urgent(Request $request): AnonymousResourceCollection
    {
        $query = \App\Models\Campaign::where('status', 'Active');

        // For SQLite compatibility, using julianday for date calculation
        $query->whereRaw('DATEDIFF(end_date, NOW()) <= 3')
              ->whereRaw('(current_amount / target_amount) >= 0.5')
              ->orderByRaw('DATEDIFF(end_date, NOW()) ASC')
              ->orderByRaw('(current_amount / target_amount) DESC');

        $urgentCampaigns = $query->paginate(10);

        return CampaignResource::collection($urgentCampaigns);
    }

}