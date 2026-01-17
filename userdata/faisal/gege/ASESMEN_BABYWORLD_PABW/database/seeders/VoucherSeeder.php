<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        // contoh data fix sesuai DB lama
        $vouchers = [
            [
                'kode_voucher'    => 'BABYDAILY15LIMITED',
                'jenis_voucher'   => 'Diskon & Cashback',
                'diskon'          => 15.00,
                'deskripsi'       => 'Diskon eksklusif',
                'tanggal_berlaku' => '2025-06-16',
                'tanggal_expired' => '2025-06-23',
                'status'          => 'non-aktif',
                'quantity'        => 0,
            ],
            [
                'kode_voucher'    => 'BABYSAVE20',
                'jenis_voucher'   => 'Diskon & Cashback',
                'diskon'          => 20.00,
                'deskripsi'       => 'Halo babyworld',
                'tanggal_berlaku' => '2025-06-17',
                'tanggal_expired' => '2025-06-18',
                'status'          => 'non-aktif',
                'quantity'        => 0,
            ],
            [
                'kode_voucher'    => 'BABYDAILY20LIMITED',
                'jenis_voucher'   => 'Diskon & Cashback',
                'diskon'          => 20.00,
                'deskripsi'       => 'Unlimited voucher',
                'tanggal_berlaku' => '2025-06-17',
                'tanggal_expired' => '2025-06-25',
                'status'          => 'aktif',
                'quantity'        => 1,
            ],
            [
                'kode_voucher'    => 'BABYSAVE15',
                'jenis_voucher'   => 'Diskon & Cashback',
                'diskon'          => 15.00,
                'deskripsi'       => 'Diskon besar besaran',
                'tanggal_berlaku' => '2025-06-17',
                'tanggal_expired' => '2025-06-18',
                'status'          => 'non-aktif',
                'quantity'        => 0,
            ],
        ];

        foreach ($vouchers as $data) {
            Voucher::create($data);
        }

        // tambahan random pakai Faker
        Voucher::factory()->count(3)->create();
    }
}