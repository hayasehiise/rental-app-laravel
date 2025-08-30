<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestGuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $member = User::firstOrCreate([
            'name' => 'Member test',
            'email' => 'member@gmail.com',
            'password' => bcrypt('membertest')
        ]);
        $member->assignRole('member');

        $guest = User::firstOrCreate([
            'name' => 'Guest test',
            'email' => 'guest@gmail.com',
            'password' => bcrypt('guesttest')
        ]);
        $guest->assignRole('guest');
    }
}
