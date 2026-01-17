<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $table = 'donations';
    protected $fillable = [
        'user_id',
        'campaign_id',
        'order_id',     
        'amount',       
        'status',       
        'snap_token',  
        'donor_name',   
        'donor_email',  
        'doa',          
        'is_anonymous'  
    ];

    /**
     * Relasi: Donasi milik satu User
     * (Inverse dari User::donations)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Donasi ditujukan untuk satu Campaign
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}