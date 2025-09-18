<?php

namespace Database\Seeders;

use App\Models\BookingType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'code' => 'hourly',
                'name' => 'Perjam',
                'monthly_limit' => null,
            ],
            [
                'code' => 'member',
                'name' => 'Member',
                'monthly_limit' => 4,
            ],
        ];

        foreach ($types as $type) {
            BookingType::firstOrCreate([
                'code' => $type['code'],
                'name' => $type['name'],
                'monthly_limit' => $type['monthly_limit'],
            ]);
        }
    }
}
