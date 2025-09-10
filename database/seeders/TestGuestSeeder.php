<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VerifyUser;
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
        VerifyUser::firstOrCreate([
            'user_id' => $member->id,
            'verified_status' => true,
            'verification_token' => null,
            'verification_expire_at' => null,
        ]);
        $member->assignRole('member');

        $guest = User::firstOrCreate([
            'name' => 'Guest test',
            'email' => 'guest@gmail.com',
            'password' => bcrypt('guesttest')
        ]);
        VerifyUser::firstOrCreate([
            'user_id' => $guest->id,
            'verified_status' => true,
            'verification_token' => null,
            'verification_expire_at' => null,
        ]);
        $guest->assignRole('guest');

        $guest2 = User::firstOrCreate([
            'name' => 'Guest 2 test',
            'email' => 'guest2@gmail.com',
            'password' => bcrypt('guest2test')
        ]);
        VerifyUser::firstOrCreate([
            'user_id' => $guest2->id,
            'verified_status' => false,
            'verification_token' => null,
            'verification_expire_at' => null,
        ]);
        $guest->assignRole('guest');
    }
}
