<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeder encargado de crear los roles principales del sistema ('administrador', 'gestor' y 'usuario'),
 * registrar todos los permisos granulares y asignarlos según el nivel de acceso de cada perfil.
 */
class RoleSeeder extends Seeder
{
    /**
     * Ejecuta el rellenado de roles y permisos limpiando previamente la caché de Spatie Permission.
     *
     * @return void
     */
    public function run(): void
    {
        // Limpiar caché de permisos antes de registrar o actualizar reglas.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /** CREAR ROLES*/
       
        $admin = Role::findOrCreate('administrador');
        $gestor = Role::findOrCreate('gestor');
        $usuario = Role::findOrCreate('usuario');

        /** CREAR PERMISOS */
        
        $permisos = [
            // Tiendas
            'ver tiendas',
            'crear tiendas',
            'editar tiendas',
            'eliminar tiendas',

            // Productos
            'ver productos',
            'crear productos',
            'editar productos',
            'eliminar productos',

            // Categorías
            'ver categorias',
            'crear categorias',
            'editar categorias',
            'eliminar categorias',

            // Reservas
            'ver reservas',
            'crear reservas',
            'eliminar reservas',
        ];

        foreach ($permisos as $permiso) {
            Permission::findOrCreate($permiso);
        }

       /**  ASIGNAR PERMISOS */ 

        // Administrador: acceso total a todos los permisos del sistema.
        $admin->givePermissionTo(Permission::all());

        // Gestor: consulta y edición sobre tiendas y productos, y consulta de categorías y reservas.
        $gestor->givePermissionTo([
            'ver tiendas',
            'editar tiendas',
            'ver productos',
            'editar productos',
            'ver categorias',
            'ver reservas',
        ]);

        // Usuario: consulta general y creación/visualización de reservas.
        $usuario->givePermissionTo([
            'ver tiendas',
            'ver productos',
            'ver categorias',
            'ver reservas',
            'crear reservas',
        ]);
    }
}


