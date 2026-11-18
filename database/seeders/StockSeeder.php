<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder encargado de poblar la tabla 'stocks' asignando cantidades iniciales aleatorias
 * a cada combinación de producto y tienda que tenga un precio registrado.
 */
class StockSeeder extends Seeder
{
    /**
     * Ejecuta el sembrado de inventario recorriendo los registros de 'precios'
     * e insertando o actualizando un stock aleatorio entre 10 y 100 unidades.
     *
     * @return void
     */
    public function run(): void
    {
        $precios = DB::table('precios')->get();

        foreach ($precios as $precio) {
            DB::table('stocks')->updateOrInsert(
                [
                    'producto_id' => $precio->producto_id,
                    'tienda_id'   => $precio->tienda_id,
                ],
                [
                    'stock'      => rand(10, 100),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
