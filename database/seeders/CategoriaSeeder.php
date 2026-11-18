<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder encargado de poblar la tabla 'categorias' con las categorías base del catálogo.
 */
class CategoriaSeeder extends Seeder
{
    /**
     * Ejecuta el sembrado de datos insertando o actualizando las categorías principales.
     *
     * @return void
     */
    public function run(): void
    {
        $categorias = [
            'Alimentación',
            'Ropa y Complementos',
            'Peluquería y Estética',
            'Ferretería',
            'Otros',
        ];

        foreach ($categorias as $index => $nombre) {
            DB::table('categorias')->updateOrInsert(
                ['id' => $index + 1],
                [
                    'denominacion' => $nombre,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]
            );
        }
    }
}

