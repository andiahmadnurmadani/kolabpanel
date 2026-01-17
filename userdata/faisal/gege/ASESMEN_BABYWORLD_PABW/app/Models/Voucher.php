<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;


    protected $table = 'tb_voucher';

    protected $fillable = [
        'kode_voucher',
        'jenis_voucher',
        'diskon',
        'deskripsi',
        'tanggal_berlaku',
        'tanggal_expired',
        'status',
        'quantity',
    ];

    protected $casts = [
        'tanggal_berlaku' => 'date',
        'tanggal_expired' => 'date',
        'diskon'          => 'float',
    ];
}