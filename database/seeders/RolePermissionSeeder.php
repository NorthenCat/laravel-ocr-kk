<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $superAdminRole = Role::create(['name' => 'super-admin']);
        $adminRole = Role::create(['name' => 'admin']);

        // Create default super admin user
        $superAdmin = User::create([
            'name' => 'Support',
            'email' => 'support@mail.com',
            'password' => bcrypt('Mitra123'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');
    }
}
