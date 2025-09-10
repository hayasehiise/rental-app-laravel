<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VerifyUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate([
            'name' => 'Akun Super Admin',
            'email' => 'super@admin.com',
            'password' => bcrypt('superadmin')
        ]);
        VerifyUser::firstOrCreate([
            'user_id' => $admin->id,
            'verified_status' => true,
            'verification_token' => null,
            'verification_expire_at' => null,
        ]);
        $admin->assignRole('admin');

        $staffAdmin = User::firstOrCreate([
            'name' => 'Staff Admin',
            'email' => 'staff@admin.com',
            'password' => bcrypt('staffadmin')
        ]);
        VerifyUser::firstOrCreate([
            'user_id' => $staffAdmin->id,
            'verified_status' => true,
            'verification_token' => null,
            'verification_expire_at' => null,
        ]);
        $staffAdmin->assignRole('staff_admin');

        $financeAdmin = User::firstOrCreate([
            'name' => 'Finance Admin',
            'email' => 'finance@admin.com',
            'password' => bcrypt('financeadmin')
        ]);
        VerifyUser::firstOrCreate([
            'user_id' => $financeAdmin->id,
            'verified_status' => true,
            'verification_token' => null,
            'verification_expire_at' => null,
        ]);
        $financeAdmin->assignRole('finance_admin');
    }
}
