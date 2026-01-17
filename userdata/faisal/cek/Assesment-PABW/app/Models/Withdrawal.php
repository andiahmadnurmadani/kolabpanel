<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi secara massal (Wajib ada untuk create/update)
    protected $fillable = [
        'campaign_id',
        'user_id',
        'amount',
        'bank_name',
        'account_number',
        'account_holder_name',
        'status',      
        'proof_file',   
        'admin_note',   
        'transferred_at', 
    ];

    // Ubah tipe data saat diambil dari database
    protected $casts = [
        'amount' => 'decimal:2',
        'transferred_at' => 'datetime',
    ];

    // --- RELASI ---

    // Setiap penarikan milik satu kampanye
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    // Setiap penarikan diajukan oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}