<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (Schema::hasTable('roles')) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            Role::firstOrCreate(['name' => 'usuario', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'gestor', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        }
    }
}
