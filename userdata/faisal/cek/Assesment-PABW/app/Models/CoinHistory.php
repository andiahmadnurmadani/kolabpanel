<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinHistory extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'reason',
        'transaction_type',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donationTransaction()
    {
        return $this->belongsTo(\App\Models\DonationTransaction::class, 'transaction_id');
    }
}
