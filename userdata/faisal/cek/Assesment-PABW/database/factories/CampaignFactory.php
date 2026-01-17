<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Campaign;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'target_amount' => $this->faker->randomElement([5000000, 10000000, 15000000, 20000000, 25000000]),
            'current_amount' => $this->faker->numberBetween(0, 5000000),
            'status' => $this->faker->randomElement(['Active', 'Completed', 'Inactive']),
            'image' => 'campaigns/default.jpg',
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
        ];
    }
}