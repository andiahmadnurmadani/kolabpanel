<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Donation;
use App\Models\VolunteerApplication;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'coins',
        'photo',
        'phone',
        'birth_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date', 
        ];
    }

    public function coinHistories()
    {
        return $this->hasMany(CoinHistory::class);
    }

    /**
     * Get the donation transactions for the user.
     */
    public function donationTransactions()
    {
        return $this->hasMany(\App\Models\DonationTransaction::class);
    }

    /**
     * Add coins to user
     */
    public function addCoins($amount, $reason = 'donation_completed')
    {
        $this->increment('coins', $amount);

        $this->coinHistories()->create([
            'amount' => $amount,
            'reason' => $reason,
            'transaction_type' => 'earned',
        ]);
    }
    /**
     * Relasi ke Model Donation (User punya banyak Donasi)
     */
    public function donations()
    {
        // Pastikan Anda sudah punya model Donation di App\Models\Donation
        return $this->hasMany(Donation::class);
    }

    /**
     * Relasi ke Model VolunteerApplication (User punya banyak Lamaran Relawan)
     */
    public function volunteerApplications()
    {
        // Pastikan Anda sudah punya model VolunteerApplication
        // Sesuaikan nama kolom foreign key jika bukan 'user_id'
        return $this->hasMany(VolunteerApplication::class);
    }


}
