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
        $resellerRole = Role::create(['name' => 'reseller', 'guard_name' => 'web']);
        $customerRole = Role::create(['name' => 'customer', 'guard_name' => 'web']);

        // Define permissions
        $permissions = [
            'users.view',
            'users.create',
            'voucher-plans.manage',
            'vouchers.generate',
            'vouchers.buy',
            'vouchers.sell',
            'vouchers.redeem',
            'transactions.view-all',
            'transactions.view-own',
            'wallet.topup',
            'audit-logs.view',
            'settings.manage',
            'reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to admin
        $adminPermissions = Permission::whereIn('name', [
            'users.view',
            'users.create',
            'voucher-plans.manage',
            'vouchers.generate',
            'transactions.view-all',
            'transactions.view-own',
            'wallet.topup',
            'audit-logs.view',
            'settings.manage',
            'reports.export',
        ])->get();
        $adminRole->syncPermissions($adminPermissions);

        // Assign permissions to reseller
        $resellerPermissions = Permission::whereIn('name', [
            'vouchers.buy',
            'vouchers.sell',
            'transactions.view-own',
            'reports.export',
        ])->get();
        $resellerRole->syncPermissions($resellerPermissions);

        // Assign permissions to customer
        $customerPermissions = Permission::whereIn('name', [
            'vouchers.redeem',
            'transactions.view-own',
        ])->get();
        $customerRole->syncPermissions($customerPermissions);

        // Create default admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@connect.ly',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($adminRole);
        
        // Create test reseller
        $reseller = User::create([
            'name' => 'Test Reseller',
            'email' => 'reseller@connect.ly',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $reseller->assignRole($resellerRole);
        $reseller->resellerProfile()->create([
            'business_name' => 'Connect Reseller Shop',
            'wallet_balance' => 1000.000,
            'commission_rate' => 5.00,
            'is_approved' => true,
        ]);
        
        // Create test customer
        $customer = User::create([
            'name' => 'Test Customer',
            'email' => 'customer@connect.ly',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $customer->assignRole($customerRole);
        $customer->customerProfile()->create([
            'national_id' => '123456789012',
            'address' => 'Tripoli, Libya',
        ]);
    }
}
