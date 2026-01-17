<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // PENTING: Import library Str untuk membuat slug

class VolunteerCampaignController extends Controller
{
    public function index()
    {
        $campaigns = VolunteerCampaign::latest()->get();
        return view('admin.relawan.index', compact('campaigns'));
    }

    public function create()
    {
        return view('admin.relawan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required',
            'lokasi' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        // --- PERBAIKAN: Generate Slug ---
        // Membuat slug dari judul agar kolom 'slug' di database tidak kosong
        $validated['slug'] = Str::slug($request->judul);
        // --------------------------------

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('volunteer_campaigns', $imageName, 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        $campaign = VolunteerCampaign::create($validated);

        return redirect()->route('admin.relawan.show', ['id' => $campaign->id])
            ->with('success', 'Kampanye relawan berhasil dibuat');
    }

    public function show($id)
    {
        $campaign = VolunteerCampaign::with('volunteers')->findOrFail($id);

        // Count volunteers by status - using the actual column name from the Volunteer model
        $pendingCount = $campaign->volunteers->where('status_verifikasi', 'pending')->count();
        $verifiedCount = $campaign->volunteers->whereIn('status_verifikasi', ['disetujui', 'Terverifikasi'])->count();

        return view('admin.relawan.show', compact('campaign', 'pendingCount', 'verifiedCount'));
    }

    public function edit($id)
    {
        $campaign = VolunteerCampaign::findOrFail($id);
        return view('admin.relawan.edit', compact('campaign'));
    }

    public function update(Request $request, $id)
    {
        $campaign = VolunteerCampaign::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required',
            'lokasi' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        // --- PERBAIKAN: Update Slug ---
        // Jika judul berubah, slug juga ikut berubah
        $validated['slug'] = Str::slug($request->judul);
        // ------------------------------

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($campaign->image && file_exists(public_path($campaign->image))) {
                unlink(public_path($campaign->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('volunteer_campaigns', $imageName, 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        $campaign->update($validated);

        return redirect()->route('admin.relawan.show', ['id' => $campaign->id])
            ->with('success', 'Kampanye relawan berhasil diperbarui');
    }

    public function toggleStatus(Request $request, $id)
    {   
        $request->validate([
            'status' => 'required|in:Aktif,Nonaktif'
        ]);

        $campaign = VolunteerCampaign::findOrFail($id);

        $oldStatus = $campaign->status;
        $newStatus = $request->status;

        // Update the status
        $campaign->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Status kampanye berhasil diperbarui',
            'campaign' => [
                'id' => $campaign->id,
                'status' => $campaign->status
            ]
        ]);
    }

    public function destroy($id)
    {
        VolunteerCampaign::findOrFail($id)->delete();
        return back()->with('success', 'Kampanye berhasil dihapus');
    }
}