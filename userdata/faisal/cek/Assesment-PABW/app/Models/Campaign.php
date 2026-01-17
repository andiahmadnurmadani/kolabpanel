<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'target_amount',
        'current_amount',
        'image',
        'end_date',
        'user_id',
        'slug',
        'status',
        'kategori',
        'yayasan',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'end_date' => 'date',
        'status' => 'string',
        'kategori' => 'string',
    ];

    // Mutator to automatically create slug from title
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = $value ?: Str::slug($this->title);
    }

    // Accessor to provide fallback for slug if not available
    public function getSlugAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // If no slug exists, generate one from the title
        return Str::slug($this->title);
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
            $folder = $pathParts[0]; // e.g., 'campaigns'
            $filename = implode('/', array_slice($pathParts, 1)); // e.g., 'image.jpg' or 'subfolder/image.jpg'

            // Generate proxy URL
            return url("/api/images/{$folder}/{$filename}");
        }

        // Fallback to direct storage access if path format is unexpected
        try {
            return Storage::url($value);
        } catch (\Exception $e) {
            return asset($value);
        }
    }

    // Relationship: Campaign belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Campaign has many donation transactions
    public function donationTransactions()
    {
        return $this->hasMany(\App\Models\DonationTransaction::class, 'campaign_id');
    }

    // Relationship: Campaign has many donations (old system)
    public function donations()
    {
        return $this->hasMany(\App\Models\Donation::class, 'campaign_id');
    }
    // Relationship: Campaign has many withdrawals
    // Penting untuk menghitung total dana yang sudah ditarik
    public function withdrawals()
    {
        return $this->hasMany(\App\Models\Withdrawal::class, 'campaign_id');
    }

    // Accessor to calculate current amount from all verified donations
    public function getCurrentAmountAttribute($value)
    {
        // If the database value is available and not null, return it
        if ($value !== null) {
            return $value;
        }

        // Otherwise, calculate from verified donations
        $verifiedTransactions = $this->donationTransactions()->where('status', 'VERIFIED')->sum('amount');
        $verifiedDonations = $this->donations()->where('status', 'paid')->sum('amount');

        return $verifiedTransactions + $verifiedDonations;
    }

    // Method to recalculate and update current amount
    public function recalculateCurrentAmount()
    {
        $verifiedTransactions = $this->donationTransactions()->where('status', 'VERIFIED')->sum('amount');
        $verifiedDonations = $this->donations()->where('status', 'paid')->sum('amount');

        $total = $verifiedTransactions + $verifiedDonations;

        $this->update(['current_amount' => $total]);

        return $total;
    }
}
