<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VolunteerCampaign;
use App\Models\VolunteerApplication;
use Illuminate\Support\Facades\Auth;

class VolunteerApplicationController extends Controller
{
    public function create($slug)
    {
        $campaign = VolunteerCampaign::where('slug', $slug)->firstOrFail();

        // Cek existing (UX agar user tidak capek isi form kalau sudah daftar)
        $existing = VolunteerApplication::where('user_id', Auth::id())
            ->where('volunteer_campaign_id', $campaign->id)
            ->first();

        if ($existing) {
            // Redirect dengan pesan error
            return redirect()->route('volunteer.campaigns.index') // Saran: Redirect ke index saja agar lebih rapi
                ->with('error', 'Anda sudah terdaftar di kampanye ini.');
        }

        return view('volunteer.register', compact('campaign'));
    }

    public function store(Request $request, $slug)
    {
        $campaign = VolunteerCampaign::where('slug', $slug)->firstOrFail();

        // 1. CEK ULANG APAKAH SUDAH DAFTAR (PENTING UNTUK KEAMANAN DATA)
        $existing = VolunteerApplication::where('user_id', Auth::id())
            ->where('volunteer_campaign_id', $campaign->id)
            ->exists();

        if ($existing) {
            return redirect()->route('volunteer.campaigns.index')
                ->with('error', 'Gagal! Anda sudah terdaftar sebelumnya.');
        }

        // 2. Validasi
        $request->validate([
            'alamat' => 'required|string|min:10',
            'posisi_dilamar' => 'required|string',
            'alasan_bergabung' => 'required|string|min:20',
            'cv' => 'required|file|mimes:pdf|max:2048',
        ]);

        // 3. Upload CV
        try {
            $cvPath = $request->file('cv')->store('cv-uploads', 'public');

            VolunteerApplication::create([
                'user_id' => Auth::id(),
                'volunteer_campaign_id' => $campaign->id,
                'alamat' => $request->alamat,
                'posisi_dilamar' => $request->posisi_dilamar,
                'alasan_bergabung' => $request->alasan_bergabung,
                'cv_path' => $cvPath,
                'status' => 'pending' // Default pending
            ]);

            // 4. Redirect Sukses
            // Pastikan halaman 'volunteer.campaigns.index' punya script SweetAlert!
            return redirect()->route('volunteer.campaigns.index')
                ->with('success', 'Formulir Anda telah terkirim! Tim kami akan segera melakukan verifikasi.');

        } catch (\Exception $e) {
            // Handle jika upload gagal atau DB error
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function checkStatus($slug)
    {
        // Check if the authenticated user is an admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Redirect admin to admin dashboard instead of user pages
            return redirect('/admin/dashboard');
        }

        $campaign = VolunteerCampaign::where('slug', $slug)->firstOrFail();

        $application = VolunteerApplication::where('user_id', Auth::id())
            ->where('volunteer_campaign_id', $campaign->id)
            ->firstOrFail();

        return view('volunteer.status', [
            'campaign'    => $campaign,
            'application' => $application,
            'status'      => $application->status
        ]);
    }
}