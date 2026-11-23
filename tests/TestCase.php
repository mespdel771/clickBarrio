<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Clase base de la que heredan todos los tests de la aplicación para preparar el entorno antes de cada prueba.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Se ejecuta automáticamente antes de cada test para limpiar la caché de permisos y asegurar que existen los roles básicos.
     *
     * @return void
     */
    protected function setUp(): void
    {
        // Llamamos primero a la configuración inicial de Laravel
        parent::setUp();

        // Comprobamos si la tabla de roles ya está creada en la base de datos de pruebas
        if (Schema::hasTable('roles')) {
            // Limpiamos la caché de Spatie para evitar fallos con permisos antiguos
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            // Creamos los tres roles principales del sistema si no existen todavía
            Role::firstOrCreate(['name' => 'usuario', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'gestor', 'guard_name' => 'web']);
            Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        }
    }
}
