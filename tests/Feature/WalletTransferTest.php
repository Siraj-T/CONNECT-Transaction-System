<?php

namespace Tests\Feature;

use App\Models\Citizen;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WalletTransferTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $sender;
    protected User $recipient;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'citizen', 'guard_name' => 'web']);
        PaymentMethod::firstOrCreate(['id' => 1], ['name' => 'Credit Card', 'is_active' => true]);

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        $this->admin->assignRole('admin');

        $this->sender = User::factory()->create([
            'name'  => 'Siraj Ashraf',
            'email' => 'siraj@test.com',
            'phone' => '+218919876543',
        ]);
        $this->sender->assignRole('citizen');
        $this->sender->wallet()->create(['balance' => 5000.00, 'currency' => 'LYD']);
        Citizen::create(['user_id' => $this->sender->id, 'name' => $this->sender->name, 'phone_number' => $this->sender->phone]);

        $this->recipient = User::factory()->create([
            'name'  => 'Omar Al-Warfali',
            'email' => 'omar@test.com',
            'phone' => '+218912345678',
        ]);
        $this->recipient->assignRole('citizen');
        $this->recipient->wallet()->create(['balance' => 2000.00, 'currency' => 'LYD']);
        Citizen::create(['user_id' => $this->recipient->id, 'name' => $this->recipient->name, 'phone_number' => $this->recipient->phone]);
    }

    public function test_citizen_can_view_wallet_balance_and_ledgers()
    {
        Sanctum::actingAs($this->sender);

        $response = $this->getJson('/api/wallet');

        $response->assertStatus(200);
        $response->assertJson([
            'balance'  => '5000.00',
            'currency' => 'LYD',
        ]);
    }

    public function test_transfer_auto_rejects_if_insufficient_balance()
    {
        Sanctum::actingAs($this->sender);

        $recipientCitizen = $this->recipient->citizen;

        $response = $this->postJson('/api/transactions', [
            'amount'       => 8000.00, // Greater than 5000.00
            'recipient_id' => $recipientCitizen->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'rejected',
        ]);
        $this->assertStringContainsString('Insufficient wallet balance', $response->json('rejection_reason'));

        // Verify transaction saved as rejected in DB
        $this->assertDatabaseHas('transactions', [
            'amount'           => '8000.00',
            'status'           => 'rejected',
            'citizen_id'       => $this->sender->citizen->id,
            'recipient_citizen_id' => $recipientCitizen->id,
        ]);

        // Balances remain untouched
        $this->assertEquals(5000.00, $this->sender->wallet->fresh()->balance);
        $this->assertEquals(2000.00, $this->recipient->wallet->fresh()->balance);
    }

    public function test_transfer_is_pending_if_sufficient_balance()
    {
        Sanctum::actingAs($this->sender);

        $recipientCitizen = $this->recipient->citizen;

        $response = $this->postJson('/api/transactions', [
            'amount'       => 1500.00,
            'recipient_id' => $recipientCitizen->id,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status'  => 'pending',
            'message' => 'Transfer submitted successfully. Pending administrator approval.',
        ]);

        $this->assertDatabaseHas('transactions', [
            'amount'           => '1500.00',
            'status'           => 'pending',
            'citizen_id'       => $this->sender->citizen->id,
            'recipient_citizen_id' => $recipientCitizen->id,
        ]);
    }

    public function test_admin_accepts_transfer_and_balances_settle_atomically()
    {
        Sanctum::actingAs($this->sender);

        $recipientCitizen = $this->recipient->citizen;

        $submitRes = $this->postJson('/api/transactions', [
            'amount'       => 1000.00,
            'recipient_id' => $recipientCitizen->id,
        ]);

        $txnId = $submitRes->json('data.id');
        $transaction = Transaction::find($txnId);

        // Admin reviews and accepts
        $acceptRes = $this->actingAs($this->admin)->put(route('admin.transactions.status.update', $transaction), [
            'action' => 'accept',
        ]);

        $acceptRes->assertRedirect();

        // Balances updated
        $this->assertEquals(4000.00, (float)$this->sender->wallet->fresh()->balance);
        $this->assertEquals(3000.00, (float)$this->recipient->wallet->fresh()->balance);

        // Double-entry ledgers recorded
        $this->assertDatabaseHas('wallet_ledgers', [
            'wallet_id' => $this->sender->wallet->id,
            'type'      => 'transfer_out',
            'amount'    => '1000.00',
        ]);

        $this->assertDatabaseHas('wallet_ledgers', [
            'wallet_id' => $this->recipient->wallet->id,
            'type'      => 'transfer_in',
            'amount'    => '1000.00',
        ]);
    }

    public function test_admin_rejects_transfer_and_balances_remain_unchanged()
    {
        Sanctum::actingAs($this->sender);

        $recipientCitizen = $this->recipient->citizen;

        $submitRes = $this->postJson('/api/transactions', [
            'amount'       => 1000.00,
            'recipient_id' => $recipientCitizen->id,
        ]);

        $txnId = $submitRes->json('data.id');
        $transaction = Transaction::find($txnId);

        // Admin reviews and rejects
        $rejectRes = $this->actingAs($this->admin)->put(route('admin.transactions.status.update', $transaction), [
            'action' => 'reject',
            'reason' => 'Suspected unauthorized activity.',
        ]);

        $rejectRes->assertRedirect();

        // Balances unchanged
        $this->assertEquals(5000.00, (float)$this->sender->wallet->fresh()->balance);
        $this->assertEquals(2000.00, (float)$this->recipient->wallet->fresh()->balance);

        // Transaction status marked as rejected with reason
        $this->assertEquals('rejected', $transaction->fresh()->status);
        $this->assertEquals('Suspected unauthorized activity.', $transaction->fresh()->rejection_reason);
    }
}
