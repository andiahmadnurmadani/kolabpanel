<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VolunteerCampaign;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    // 1. READ (Menampilkan Daftar)
    public function index()
    {
        $campaigns = VolunteerCampaign::latest()->paginate(10);
        return view('admin.relawan.index', compact('campaigns'));
    }

    // 2. CREATE (Menampilkan Form Tambah)
    public function create()
    {
        return view('admin.relawan.create');
    }

    // 3. STORE (Menyimpan Data Baru)
    public function store(Request $request)
    {
        // Validasi Input
        $validated = $request->validate([
            'judul'           => 'required|max:255', // Pastikan name="judul" di form HTML
            'kategori'        => 'required',
            'lokasi'          => 'required',
            'kuota_total'     => 'required|numeric|min:1',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi'       => 'required',
            'image'           => 'required|image|file|max:2048'
        ]);

        // Upload Gambar
        if ($request->file('image')) {
            $validated['image'] = $request->file('image')->store('campaign-images', 'public');
        }

        // Menggunakan 'judul' sebagai dasar slug + timestamp agar unik
        $validated['slug'] = Str::slug($request->judul) . '-' . time();

        // Set nilai default lainnya
        $validated['kuota_terisi'] = 0;
        $validated['status'] = 'Aktif';

        // Simpan ke Database
        VolunteerCampaign::create($validated);

        return redirect()->route('admin.relawan.index')
            ->with('success', 'Kampanye berhasil diterbitkan! Data kini tampil di halaman publik.');
    }

    // 4. EDIT (Menampilkan Form Edit)
    public function edit($id)
    {
        $campaign = VolunteerCampaign::findOrFail($id);
        return view('admin.relawan.edit', compact('campaign'));
    }

    // 5. UPDATE (Menyimpan Perubahan)
    public function update(Request $request, $id)
    {
        $campaign = VolunteerCampaign::findOrFail($id);

        $validated = $request->validate([
            'judul'           => 'required|max:255',
            'kategori'        => 'required',
            'lokasi'          => 'required',
            'kuota_total'     => 'required|numeric|min:1',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'deskripsi'       => 'required',
            'image'           => 'nullable|image|file|max:2048' // Image nullable saat edit
        ]);

        // Cek jika ada gambar baru yang diupload
        if ($request->file('image')) {
            // Hapus gambar lama
            if ($campaign->image) {
                Storage::delete('public/' . $campaign->image);
            }
            // Simpan gambar baru
            $validated['image'] = $request->file('image')->store('campaign-images', 'public');
        }

        // --- PERBAIKAN: UPDATE SLUG JIKA JUDUL BERUBAH ---
        if ($request->judul != $campaign->judul) {
            $validated['slug'] = Str::slug($request->judul) . '-' . time();
        }

        // Update Database
        $campaign->update($validated);

        return redirect()->route('admin.relawan.index')
            ->with('success', 'Kampanye berhasil diperbarui!');
    }

    // 6. DESTROY (Menghapus Data)
    public function destroy($id)
    {
        $campaign = VolunteerCampaign::findOrFail($id);

        // Hapus gambar dari storage
        if ($campaign->image) {
            Storage::delete('public/' . $campaign->image);
        }

        $campaign->delete();

        return redirect()->route('admin.relawan.index')
            ->with('success', 'Kampanye telah dihapus.');
    }
}