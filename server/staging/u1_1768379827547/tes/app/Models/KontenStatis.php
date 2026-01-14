<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontenStatis extends Model
{
    protected $table = 'konten_statis';
    protected $fillable = [
        'key',     // e.g. "site_logo", "hero_title", "footer_text"
        'value',   // teks atau path
        'type'     // optional: "text","image"
    ];
    public $timestamps = true;
}
