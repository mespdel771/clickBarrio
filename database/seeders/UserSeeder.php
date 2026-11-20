<?php

namespace Database\Seeders;

use App\Models\Tienda;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder encargado de rellenar la tabla 'users' creando los usuarios de prueba
 * con sus respectivos roles ('administrador', 'usuario' y 'gestor') y vinculando
 * cada gestor como propietario de su correspondiente tienda.
 */
class UserSeeder extends Seeder
{
    /**
     * Ejecuta el rellenado de usuarios insertando o actualizando el administrador,
     * los clientes de prueba y un gestor asignado para cada tienda registrada.
     *
     * @return void
     */
    public function run(): void
    {
       /**ADMINISTRADOR */
       
        $admin = User::updateOrCreate(
            [
                'email' => 'admin@gmail.com',
            ],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );

        $admin->syncRoles(['administrador']);

        /** USUARIO PRINCIPAL */
       
        $usuario = User::updateOrCreate(
            [
                'email' => 'usuario@gmail.com',
            ],
            [
                'name'     => 'Usuario',
                'password' => Hash::make('password'),
            ]
        );

        $usuario->syncRoles(['usuario']);

        /** USUARIOS EXTRA */ 

        for ($i = 1; $i <= 5; $i++) {
            $user = User::updateOrCreate(
                [
                    'email' => "user{$i}@gmail.com",
                ],
                [
                    'name'     => "Usuario {$i}",
                    'password' => Hash::make('password'),
                ]
            );

            $user->syncRoles(['usuario']);
        }

       /** GESTORES POR TIENDA */ 
       
        // El nombre del usuario gestor es genérico e independiente del nombre comercial de la tienda.
        $tiendas = Tienda::orderBy('id')->get();

        foreach ($tiendas as $tienda) {
            $gestor = User::updateOrCreate(
                [
                    'email' => 'gestor' . $tienda->id . '@gmail.com',
                ],
                [
                    'name'     => 'Gestor ' . $tienda->id,
                    'password' => Hash::make('password'),
                ]
            );

            // Asignar rol gestor al usuario.
            $gestor->syncRoles(['gestor']);

            // Asignar gestor a la tienda manteniendo la relación inversa limpia.
            $tienda->update([
                'user_id' => $gestor->id,
            ]);
        }
    }
}
