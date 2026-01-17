<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VolunteerCampaign extends Model
{
    use HasFactory;

    // 1. Beritahu Laravel nama tabelnya (karena tidak standar 'campaigns')
    protected $table = 'volunteer_campaigns';

    // 2. Izinkan semua kolom diisi (Mass Assignment)
    protected $guarded = ['id'];

    // 3. Helper untuk Progress Bar Frontend
    public function getProgressAttribute()
    {
        if ($this->kuota_total == 0) return 0;
        return round(($this->kuota_terisi / $this->kuota_total) * 100);
    }

    // Accessor to handle different image path formats
    public function getImageAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // If it's already a proxy URL or external URL, return as is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // For local storage files, return proxy URL instead of direct storage URL
        // This helps with Flutter access and CORS issues
        $pathParts = explode('/', $value);
        if (count($pathParts) >= 2) {
            $folder = $pathParts[0]; // e.g., 'volunteer_campaigns'
            $filename = implode('/', array_slice($pathParts, 1)); // e.g., 'image.jpg' or 'subfolder/image.jpg'

            // Generate proxy URL
            return url("/api/images/{$folder}/{$filename}");
        }

        // Fallback to direct storage access if path format is unexpected
        return url('storage/' . $value);
    }

    public function volunteers()
    {
        return $this->hasMany(VolunteerApplication::class, 'volunteer_campaign_id');
    }
}
