<?php

namespace Database\Seeders;

use App\Models\User;
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
        $admin->assignRole('admin');

        $staffAdmin = User::firstOrCreate([
            'name' => 'Staff Admin',
            'email' => 'staff@admin.com',
            'password' => bcrypt('staffadmin')
        ]);
        $staffAdmin->assignRole('staff_admin');

        $financeAdmin = User::firstOrCreate([
            'name' => 'Finance Admin',
            'email' => 'finance@admin.com',
            'password' => bcrypt('financeadmin')
        ]);
        $financeAdmin->assignRole('finance_admin');
    }
}
