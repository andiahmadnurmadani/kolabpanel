<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;   

class ProductFactory extends Factory
{
    protected $model = Product::class; 

    public function definition(): array
    {
        $images = [
            "1.jpg",
            "2.jpg",
            "3.jpg",
            "4.jpg",
            "5.jpg",
            "6.jpg",
            "7.jpg",
            "8.jpg",
            "9.jpg",
            "10.jpg",
        ];

        return [
            'nama_produk' => $this->faker->words(2, true),
            'kategori' => $this->faker->randomElement(['Bundle', 'Voucher', 'Produk Baru']),
            'deskripsi' => $this->faker->sentence(12),
            'harga' => $this->faker->numberBetween(10000, 200000),
            'foto' => 'assets/img/Voucher/' . $this->faker->randomElement($images)
        ];
    }
}