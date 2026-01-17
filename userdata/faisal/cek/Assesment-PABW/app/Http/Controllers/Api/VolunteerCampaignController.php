<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\VolunteerCampaign;
use App\Http\Resources\VolunteerCampaignResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class VolunteerCampaignController extends Controller
{
    /**
     * Get all volunteer campaigns (accessible by all authenticated users)
     */
    public function index(): JsonResponse
    {
        $volunteerCampaigns = VolunteerCampaign::orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'message' => 'Volunteer campaigns retrieved successfully',
            'data' => VolunteerCampaignResource::collection($volunteerCampaigns)
        ]);
    }

    /**
     * Get specific volunteer campaign (accessible by all authenticated users)
     */
    public function show($id): JsonResponse
    {
        $volunteerCampaign = VolunteerCampaign::with('volunteers')->find($id);

        if (!$volunteerCampaign) {
            return response()->json([
                'message' => 'Volunteer campaign not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Volunteer campaign retrieved successfully',
            'data' => new VolunteerCampaignResource($volunteerCampaign)
        ]);
    }

    /**
     * Create new volunteer campaign (admin only)
     */
    public function store(Request $request): JsonResponse
    {
        // Only allow admin users
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:volunteer_campaigns,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date|before_or_equal:tanggal_selesai',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kategori' => 'required|in:Pendidikan,Bencana,Kesehatan,Lingkungan,Sosial',
            'kuota_total' => 'required|integer|min:1',
            'status' => 'required|in:Aktif,Nonaktif,Selesai'
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('volunteer_campaigns', 'public');
            $data['image'] = $imagePath;
        }

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['judul']);
        }

        $volunteerCampaign = VolunteerCampaign::create($data);

        return response()->json([
            'message' => 'Volunteer campaign created successfully',
            'data' => new VolunteerCampaignResource($volunteerCampaign)
        ], 201);
    }

    /**
     * Update volunteer campaign (admin only)
     */
    public function update(Request $request, $id): JsonResponse
    {
        // Only allow admin users
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $volunteerCampaign = VolunteerCampaign::find($id);

        if (!$volunteerCampaign) {
            return response()->json([
                'message' => 'Volunteer campaign not found'
            ], 404);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:volunteer_campaigns,slug,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date|before_or_equal:tanggal_selesai',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kategori' => 'required|in:Pendidikan,Bencana,Kesehatan,Lingkungan,Sosial',
            'kuota_total' => 'required|integer|min:1',
            'status' => 'required|in:Aktif,Nonaktif,Selesai'
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($volunteerCampaign->image && Storage::exists('public/' . $volunteerCampaign->image)) {
                Storage::delete('public/' . $volunteerCampaign->image);
            }

            $imagePath = $request->file('image')->store('volunteer_campaigns', 'public');
            $data['image'] = $imagePath;
        }

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['judul']);
        }

        $volunteerCampaign->update($data);

        return response()->json([
            'message' => 'Volunteer campaign updated successfully',
            'data' => new VolunteerCampaignResource($volunteerCampaign)
        ]);
    }

    /**
     * Delete volunteer campaign (admin only)
     */
    public function destroy($id): JsonResponse
    {
        // Only allow admin users
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        $volunteerCampaign = VolunteerCampaign::find($id);

        if (!$volunteerCampaign) {
            return response()->json([
                'message' => 'Volunteer campaign not found'
            ], 404);
        }

        // Delete image if exists
        if ($volunteerCampaign->image && Storage::exists('public/' . $volunteerCampaign->image)) {
            Storage::delete('public/' . $volunteerCampaign->image);
        }

        $volunteerCampaign->delete();

        return response()->json([
            'message' => 'Volunteer campaign deleted successfully'
        ]);
    }
}