<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Diskon default untuk member
        Discount::create([
            'name' => 'Diskon Member',
            'percentage' => 20,
            'is_member_only' => true,
            'start_time' => null,
            'end_time' => null,
        ]);
    }
}
