<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        \App\Models\PaymentMethod::insert([
            ['name' => 'Credit Card', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Transfer', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PayPal', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
