<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSanctumTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_can_login_via_sanctum_and_create_transactions(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $method = PaymentMethod::create(['name' => 'Credit Card', 'is_active' => true]);

        // 1. Test Login Failure
        $failResponse = $this->postJson('/api/login', [
            'email'    => 'citizen@citizen.com',
            'password' => 'wrongpassword',
        ]);
        $failResponse->assertStatus(401);

        // 2. Test Login Success
        $loginResponse = $this->postJson('/api/login', [
            'email'    => 'citizen@citizen.com',
            'password' => 'password',
        ]);
        $loginResponse->assertStatus(200);
        $loginResponse->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
            'user' => ['id', 'name', 'email', 'phone'],
        ]);

        $token = $loginResponse->json('access_token');
        $this->assertNotEmpty($token);

        // 3. Test Unauthorized Access to /api/transactions without token
        $unauthResponse = $this->postJson('/api/transactions', [
            'amount'            => 150.00,
            'payment_method_id' => $method->id,
        ]);
        $unauthResponse->assertStatus(401);

        // 4. Test Authorized Transaction Creation (Name and Phone pulled automatically from User)
        $authResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/transactions', [
                'amount'            => 275.50,
                'payment_method_id' => $method->id,
                // Even if client attempts to override name, server trusts only authenticated user
                'sender_name'       => 'Hacker Name',
            ]);

        $authResponse->assertStatus(201);
        $authResponse->assertJson([
            'message' => 'Transaction submitted successfully',
            'data' => [
                'amount' => '275.50',
                'status' => 'pending',
                'citizen' => [
                    'name'         => 'Jane Doe',
                    'phone_number' => '+1987654321',
                ],
            ],
        ]);

        $this->assertDatabaseHas('transactions', [
            'amount' => '275.50',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('citizens', [
            'name'         => 'Jane Doe',
            'phone_number' => '+1987654321',
        ]);

        // 5. Test Logout
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');
        $logoutResponse->assertStatus(200);
    }
}
