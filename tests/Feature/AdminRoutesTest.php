<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AuditLog;
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
}
