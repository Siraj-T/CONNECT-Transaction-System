<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\PaymentMethod;
use App\Models\Citizen;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Database\Seeders\RolePermissionSeeder;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard_and_audit_logs_separately(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create([
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Test Dashboard
        $dashResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertViewIs('admin.dashboard');
        $dashResponse->assertSee('Overview');
        $dashResponse->assertSee('Total Accepted Volume');
        $dashResponse->assertSee('Transactions');
        // Ensure audit trail table is NOT on the dashboard
        $dashResponse->assertDontSee('Audit History Records');
        // Check active class on dashboard link
        $dashResponse->assertSee('href="' . route('admin.dashboard') . '" class="nav-link active"', false);
        $dashResponse->assertSee('href="' . route('admin.audit-logs') . '" class="nav-link "', false);

        // Test Audit Logs page
        $auditResponse = $this->actingAs($admin)->get(route('admin.audit-logs'));
        $auditResponse->assertStatus(200);
        $auditResponse->assertViewIs('admin.audit-logs');
        $auditResponse->assertSee('Audit Trail');
        $auditResponse->assertSee('Forensic record of all administrative transaction decisions');
        $auditResponse->assertSee('Total Decisions Logged');
        $auditResponse->assertSee('Audit History Records');
        // Check active class on audit logs link
        $auditResponse->assertSee('href="' . route('admin.audit-logs') . '" class="nav-link active"', false);
        $auditResponse->assertSee('href="' . route('admin.dashboard') . '" class="nav-link "', false);
    }

    public function test_live_dashboard_endpoint_returns_json_and_handles_versioning(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $method = PaymentMethod::create(['name' => 'Card', 'is_active' => true]);
        $citizen = Citizen::create(['name' => 'John Doe', 'phone_number' => '0100000000']);

        $txn = Transaction::create([
            'unique_reference_number' => 'TRX-TEST1234',
            'amount' => 500.00,
            'status' => 'pending',
            'citizen_id' => $citizen->id,
            'payment_method_id' => $method->id,
        ]);

        // Request without version -> returns changed: true and rows
        $response = $this->actingAs($admin)->getJson(route('admin.dashboard.live'));
        $response->assertStatus(200);
        $response->assertJson([
            'changed' => true,
        ]);
        $response->assertJsonStructure(['changed', 'version', 'metrics', 'rowsHtml', 'paginationHtml']);
        $version = $response->json('version');

        // Request with matching version -> returns changed: false (zero overhead)
        $cachedResponse = $this->actingAs($admin)->getJson(route('admin.dashboard.live', ['v' => $version]));
        $cachedResponse->assertStatus(200);
        $cachedResponse->assertExactJson([
            'changed' => false,
            'version' => $version,
        ]);
    }
}
