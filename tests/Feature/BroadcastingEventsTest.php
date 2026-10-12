<?php

namespace Tests\Feature;

use App\Events\TransactionCreated;
use App\Events\TransactionStatusUpdated;
use App\Models\Citizen;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BroadcastingEventsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'citizen', 'guard_name' => 'web']);
        PaymentMethod::firstOrCreate(['id' => 1], ['name' => 'Credit Card', 'is_active' => true]);
    }

    public function test_transaction_created_event_is_broadcast_when_api_transaction_submitted()
    {
        Event::fake([TransactionCreated::class]);

        $user = User::factory()->create([
            'name'  => 'John Doe',
            'email' => 'john@test.com',
            'phone' => '+1555000111',
        ]);
        $user->assignRole('citizen');
        $user->wallet()->create(['balance' => 1000.00, 'currency' => 'LYD']);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/transactions', [
            'amount'            => 500,
            'payment_method_id' => 1,
        ]);

        $response->assertStatus(201);

        Event::assertDispatched(TransactionCreated::class, function ($event) use ($user) {
            return $event->transaction->amount == 500
                && $event->transaction->citizen->name === 'John Doe';
        });
    }

    public function test_transaction_is_auto_rejected_when_wallet_balance_is_insufficient()
    {
        Event::fake([TransactionCreated::class]);

        $user = User::factory()->create([
            'name'  => 'Bob Builder',
            'email' => 'bob@test.com',
            'phone' => '+1555000222',
        ]);
        $user->assignRole('citizen');
        $user->wallet()->create(['balance' => 100.00, 'currency' => 'LYD']);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/transactions', [
            'amount'            => 500,
            'payment_method_id' => 1,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'rejected',
        ]);
        $this->assertStringContainsString('Auto-rejected: Insufficient wallet balance', $response->json('rejection_reason'));

        Event::assertDispatched(TransactionCreated::class, function ($event) {
            return $event->transaction->status === 'rejected';
        });
    }

    public function test_transaction_status_updated_event_is_broadcast_on_admin_action()
    {
        Event::fake([TransactionStatusUpdated::class]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $citizen = Citizen::create(['name' => 'Alice', 'phone_number' => '+1555999888']);
        $txn = Transaction::create([
            'unique_reference_number' => 'TRX-TESTBROADCAST',
            'amount'                  => 1000,
            'status'                  => 'pending',
            'citizen_id'              => $citizen->id,
            'payment_method_id'       => 1,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.transactions.status.update', $txn), [
            'action' => 'accept',
        ]);

        $response->assertRedirect();

        Event::assertDispatched(TransactionStatusUpdated::class, function ($event) use ($txn) {
            return $event->transaction->id === $txn->id
                && $event->transaction->status === 'accepted';
        });
    }
}
