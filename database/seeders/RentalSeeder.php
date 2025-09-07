<?php

namespace Database\Seeders;

use App\Models\Rental;
use App\Models\RentalUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rental::factory(50)->create()->each(function ($rental) {
            RentalUnit::factory(10)->create([
                'rental_id' => $rental->id,
            ]);
        });
    }
}
