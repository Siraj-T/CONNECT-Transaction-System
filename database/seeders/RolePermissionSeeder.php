<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define roles
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);

        // Define permissions
        $permissions = [
            'transactions.view-all',
            'transactions.approve',
            'transactions.reject',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to admin
        $adminPermissions = Permission::whereIn('name', [
            'transactions.view-all',
            'transactions.approve',
            'transactions.reject',
        ])->get();
        $adminRole->syncPermissions($adminPermissions);

        // Create default admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@financial-transaction-system.local',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($adminRole);
    }
}
