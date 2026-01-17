<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VolunteerApplication;
use App\Models\VolunteerCampaign;
use App\Notifications\VolunteerStatusChanged;

class VolunteerVerificationController extends Controller
{

   public function index()
    {
        $allVolunteers = VolunteerApplication::with(['user', 'campaign'])
            ->latest()
            ->paginate(10);

        return view('admin.verifikasi-relawan.index', compact('allVolunteers'));
    }

  // 1. MENAMPILKAN DETAIL
    public function show($id)
    {
        $volunteer = VolunteerApplication::with(['user', 'campaign'])->findOrFail($id);
        return view('admin.verifikasi-relawan.show', compact('volunteer'));
    }

    // 2. PROSES TERIMA / TOLAK
    // CODE PERBAIKAN DI CONTROLLER
public function update(Request $request, $id)
{
    // 1. Ambil Data Aplikasi
    $application = \App\Models\VolunteerApplication::findOrFail($id);
    
    // 2. [PENTING] DEFINISIKAN CAMPAIGN DARI RELASI APLIKASI
    // Pastikan nama relasi di model VolunteerApplication adalah 'campaign' atau 'volunteerCampaign'
    // Sesuaikan dengan Model Anda (biasanya 'campaign' atau 'volunteer_campaign')
    $campaign = $application->campaign; 

    // ... Logika simpan status (approve/reject) ...
    $application->status = $request->status;
    $application->save();

    // Logika Kuota (Hanya jika Approved)
    if ($request->status == 'approved') {
        $campaign->increment('kuota_terisi');
    }

    // 3. Kirim Notifikasi
    $user = $application->user;
    if ($user) {
        $user->notify(new \App\Notifications\VolunteerStatusChanged(
            // Ganti 'title' jadi 'judul' jika di database kolomnya 'judul'
            $campaign->judul, 
            $request->status,
            $campaign->slug
        ));
    }

    $message = $request->status == 'approved' ? 'Relawan diterima!' : 'Lamaran ditolak.';
    return redirect()->route('admin.verifikasi-relawan.index')->with('success', $message);
}
}