<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DonationCampaignController extends Controller
{
    // 1. READ (Menampilkan Daftar)
    public function index()
    {
        $campaigns = Campaign::latest()->paginate(10);
        return view('admin.campaigns.index', compact('campaigns'));
    }

    // 2. CREATE (Menampilkan Form Tambah)
    public function create()
    {
        return view('admin.campaigns.create');
    }

    // 3. STORE (Menyimpan Data Baru)
    public function store(Request $request)
    {
        // Validasi Input
        $validated = $request->validate([
            'title'           => 'required|max:255',
            'description'     => 'required',
            'target_amount'   => 'required|numeric|min:1',
            'current_amount'  => 'nullable|numeric|min:0',
            'status'          => 'required|in:Active,Completed,Inactive,Pending',
            'kategori'        => 'required|in:Lingkungan,Kesehatan,Pendidikan,Sosial Kemanusiaan,Bencana Alam',
            'yayasan'         => 'nullable|string|max:255',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'end_date'        => 'required|date'
        ]);

        // Upload Gambar
        if ($request->file('image')) {
            $validated['image'] = $request->file('image')->store('campaigns', 'public');
        }

        // Set default values if not provided
        $validated['current_amount'] = $validated['current_amount'] ?? 0;

        // Generate slug from title
        $validated['slug'] = Str::slug($request->title) . '-' . time();

        // Set user_id to currently authenticated admin
        $validated['user_id'] = auth()->id();

        // Simpan ke Database
        Campaign::create($validated);

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanye donasi berhasil diterbitkan! Data kini tampil di halaman publik.');
    }

    // 4. EDIT (Menampilkan Form Edit)
    public function edit($id)
    {
        $campaign = Campaign::findOrFail($id);
        return view('admin.campaigns.edit', compact('campaign'));
    }

    // 5. UPDATE (Menyimpan Perubahan)
    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'title'           => 'required|max:255',
            'description'     => 'required',
            'target_amount'   => 'required|numeric|min:1',
            'current_amount'  => 'nullable|numeric|min:0',
            'status'          => 'required|in:Active,Completed,Inactive,Pending',
            'kategori'        => 'required|in:Lingkungan,Kesehatan,Pendidikan,Sosial Kemanusiaan,Bencana Alam',
            'yayasan'         => 'nullable|string|max:255',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'end_date'        => 'required|date'
        ]);

        // Cek jika ada gambar baru yang diupload
        if ($request->file('image')) {
            // Hapus gambar lama
            if ($campaign->image) {
                Storage::delete('public/' . $campaign->image);
            }
            // Simpan gambar baru
            $validated['image'] = $request->file('image')->store('campaigns', 'public');
        }

        // Update slug if title has changed
        if ($request->title != $campaign->title) {
            $validated['slug'] = Str::slug($request->title) . '-' . time();
        } else {
            // Don't update the slug if title hasn't changed
            unset($validated['slug']);
        }

        // Update Database
        $campaign->update($validated);

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanye donasi berhasil diperbarui!');
    }

    // 6. DESTROY (Menghapus Data)
    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);

        // Hapus gambar dari storage
        if ($campaign->image) {
            Storage::delete('public/' . $campaign->image);
        }

        $campaign->delete();

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanye donasi telah dihapus.');
    }
}