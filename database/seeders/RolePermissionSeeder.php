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
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $citizenRole = Role::firstOrCreate(['name' => 'citizen', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Define permissions
        $permissions = [
            'transactions.view-all',
            'transactions.approve',
            'transactions.reject',
            'transactions.create',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to admin
        $adminPermissions = Permission::whereIn('name', [
            'transactions.view-all',
            'transactions.approve',
            'transactions.reject',
        ])->get();
        $adminRole->syncPermissions($adminPermissions);

        // Assign create permission to citizen/customer
        $citizenRole->syncPermissions(['transactions.create']);
        $customerRole->syncPermissions(['transactions.create']);

        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $admin->assignRole($adminRole);

        // Create default citizen user (Jane Doe)
        $citizen1 = User::firstOrCreate(
            ['email' => 'citizen@citizen.com'],
            [
                'name' => 'Jane Doe',
                'phone' => '+1987654321',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $citizen1->syncRoles([$citizenRole, $customerRole]);
        \App\Models\Citizen::updateOrCreate(
            ['phone_number' => $citizen1->phone],
            ['user_id' => $citizen1->id, 'name' => $citizen1->name]
        );

        // Create secondary citizen user (Ahmed Mohamed)
        $citizen2 = User::firstOrCreate(
            ['email' => 'ahmed@citizen.com'],
            [
                'name' => 'Ahmed Mohamed',
                'phone' => '+1234567890',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $citizen2->syncRoles([$citizenRole, $customerRole]);
        \App\Models\Citizen::updateOrCreate(
            ['phone_number' => $citizen2->phone],
            ['user_id' => $citizen2->id, 'name' => $citizen2->name]
        );

        // 5 Extra Libyan Citizen Accounts
        $libyanCitizens = [
            [
                'name'  => 'Omar Al-Warfali',
                'email' => 'omar@citizen.ly',
                'phone' => '+218912345678',
            ],
            [
                'name'  => 'Fatima Al-Zintani',
                'email' => 'fatima@citizen.ly',
                'phone' => '+218923456789',
            ],
            [
                'name'  => 'Tarek Ben Ali',
                'email' => 'tarek@citizen.ly',
                'phone' => '+218917654321',
            ],
            [
                'name'  => 'Mariam Al-Tarhuni',
                'email' => 'mariam@citizen.ly',
                'phone' => '+218928765432',
            ],
            [
                'name'  => 'Yousef Al-Misrati',
                'email' => 'yousef@citizen.ly',
                'phone' => '+218945678901',
            ],
            [
                'name'  => 'Siraj Ashraf',
                'email' => 'siraj@gmail.com',
                'phone' => '+218919876543',
            ],
        ];

        foreach ($libyanCitizens as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'phone'             => $data['phone'],
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]
            );
            $user->syncRoles([$citizenRole, $customerRole]);

            \App\Models\Citizen::updateOrCreate(
                ['phone_number' => $data['phone']],
                ['user_id' => $user->id, 'name' => $data['name']]
            );
        }
    }
}
