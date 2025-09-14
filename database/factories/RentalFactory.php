<?php

namespace Database\Factories;

use App\Models\RentalCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental>
 */
class RentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Rental',
            'category_id' => RentalCategory::inRandomOrder()->first()?->id,
            'description' => $this->faker->paragraph()
        ];
    }
}
