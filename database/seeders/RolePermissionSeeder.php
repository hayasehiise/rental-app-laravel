<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permission
        $permissions = [
            'manage_user',
            'manage_rental',
            'view_report',
            'get_discount',
            'normal_checkout',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $admin = Role::create(['name' => 'admin']);
        $staffAdmin = Role::create(['name' => 'staff_admin']);
        $financeAdmin = Role::create(['name' => 'finance_admin']);
        $member = Role::create(['name' => 'member']);
        $guest = Role::create(['name' => 'guest']);

        //Assing Permission
        $admin->givePermissionTo([Permission::all()]);
        $staffAdmin->givePermissionTo(['manage_rental']);
        $financeAdmin->givePermissionTo(['view_report']);
        $member->givePermissionTo(['get_discount']);
        $guest->givePermissionTo(['normal_checkout']);
    }
}
