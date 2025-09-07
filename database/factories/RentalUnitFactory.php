<?php

namespace Database\Factories;

use App\Models\Rental;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RentalUnit>
 */
class RentalUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_id' => Rental::factory(),
            'name' => $this->faker->word() . ' Unit',
            'price' => $this->faker->randomFloat(0, 500000, 2000000),
            'is_available' => $this->faker->boolean(100)
        ];
    }
}
