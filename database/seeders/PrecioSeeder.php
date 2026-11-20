<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder encargado de rellenar la tabla 'precios' asignando precios aleatorios
 * a un subconjunto de productos correspondientes a la categoría de cada tienda.
 */
class PrecioSeeder extends Seeder
{
    /**
     * Ejecuta el rellenado de precios seleccionando entre el 30 % y el 70 % de los productos
     * de la categoría de cada tienda e insertando o actualizando su precio correspondiente.
     *
     * @return void
     */
    public function run(): void
    {
        $tiendas = DB::table('tiendas')->get();

        foreach ($tiendas as $tienda) {
            // Productos de la misma categoría que la tienda.
            $productosQuery = DB::table('productos')
                ->where('categoria_id', $tienda->categoria_id);

            $total = $productosQuery->count();

            if ($total === 0) {
                continue;
            }

            // Calcula cuántos productos asignar (entre el 30 % y el 70 %).
            $cantidad = rand(
                (int) ceil($total * 0.3),
                (int) ceil($total * 0.7)
            );

            // Selecciona productos aleatorios de la categoría.
            $productos = $productosQuery
                ->inRandomOrder()
                ->limit($cantidad)
                ->get();

            // Inserta o actualiza los precios para cada producto seleccionado.
            foreach ($productos as $producto) {
                DB::table('precios')->updateOrInsert(
                    [
                        'producto_id' => $producto->id,
                        'tienda_id'   => $tienda->id,
                    ],
                    [
                        'precio'     => rand(1, 30) + 0.99,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
