<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Address;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'security_question',
        'security_answer',
        'role',
        'picture'// konsisten dengan controller
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'security_answer',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Relasi 1 user punya 1 address
     */
    public function address()
    {
        return $this->hasOne(Address::class);
    }
}
