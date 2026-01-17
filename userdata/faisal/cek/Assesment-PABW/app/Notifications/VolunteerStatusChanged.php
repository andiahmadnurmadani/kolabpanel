<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Campaign; 
use App\Models\Volunteer; 

class VolunteerStatusChanged extends Notification
{
    use Queueable;

    protected $campaignTitle;
    protected $status;
    protected $campaignSlug; 

    public function __construct($campaignTitle, $status, $campaignSlug)
    {
        $this->campaignTitle = $campaignTitle;
        $this->status = $status;
        $this->campaignSlug = $campaignSlug;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function checkStatus($slug)
{
    // 1. Cari Kampanye berdasarkan Slug
    $campaign = Campaign::where('slug', $slug)->firstOrFail();

    // 2. Cari Lamaran User untuk kampanye ini
    // Asumsi: Ada tabel 'volunteers' yang punya user_id dan campaign_id
    $application = Volunteer::where('user_id', auth()->id())
                            ->where('campaign_id', $campaign->id)
                            ->firstOrFail();

    // 3. Tampilkan View Status
    return view('volunteer.status', [
        'campaign' => $campaign,
        'status' => $application->status // pastikan kolom status ada di tabel volunteers
    ]);
}

   public function toArray($notifiable)
{
    $isApproved = $this->status === 'approved';
    
    // PERUBAHAN DISINI: Arahkan ke route status yang baru kita buat
    $targetUrl = route('volunteer.application.status', $this->campaignSlug);

    return [
        'title'   => $isApproved ? 'Lamaran Diterima! 🎉' : 'Lamaran Ditolak',
        'message' => $isApproved 
            ? "Selamat! Anda diterima sebagai relawan untuk \"{$this->campaignTitle}\"." 
            : "Mohon maaf, lamaran Anda untuk \"{$this->campaignTitle}\" belum dapat kami terima.",
        'status'  => $this->status,
        'url'     => $targetUrl, // <--- User akan diarahkan ke halaman status yang personal
        'icon'    => $isApproved ? 'fas fa-check-circle' : 'fas fa-times-circle',
        'color'   => $isApproved ? 'text-green-500' : 'text-red-500',
    ];
}
}