<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles exist for tests
        if (\Illuminate\Support\Facades\Schema::hasTable('roles')) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'reseller', 'guard_name' => 'web']);
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        }
    }
}
