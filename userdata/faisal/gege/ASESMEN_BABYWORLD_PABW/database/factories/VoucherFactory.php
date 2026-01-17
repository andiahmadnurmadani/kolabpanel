<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 days', '+2 days');
        $end   = (clone $start)->modify('+7 days');

        return [
            'kode_voucher'    => strtoupper('BABY' . $this->faker->bothify('####')),
            'jenis_voucher'   => 'Diskon & Cashback',
            'diskon'          => $this->faker->randomElement([10, 15, 20]),
            'deskripsi'       => $this->faker->sentence(),
            'tanggal_berlaku' => $start->format('Y-m-d'),
            'tanggal_expired' => $end->format('Y-m-d'),
            'status'          => 'aktif',
            'quantity'        => $this->faker->numberBetween(0, 10),
        ];
    }
}