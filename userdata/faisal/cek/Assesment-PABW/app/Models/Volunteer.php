<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Volunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'email',
        'whatsapp',
        'motivasi',
        'keahlian',
        'status_verifikasi',
        'volunteer_campaign_id',
    ];

    protected $casts = [
        'status_verifikasi' => 'string',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(VolunteerCampaign::class, 'volunteer_campaign_id');
    }
}