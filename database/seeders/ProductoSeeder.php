<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder encargado de rellenar la tabla 'productos' con el catálogo inicial
 * distribuido por categorías (Alimentación, Ropa, Peluquería, Ferretería y Otros).
 */
class ProductoSeeder extends Seeder
{
    /**
     * Ejecuta el rellenado de datos insertando o actualizando los productos base del catálogo.
     *
     * @return void
     */
    public function run(): void
    {
        $productos = [
            // Alimentación
            ['categoria_id' => 1, 'denominacion' => 'Viena de pan', 'imagen' => 'viena.png'],
            ['categoria_id' => 1, 'denominacion' => 'Ketchup', 'imagen' => 'ketchup.png'],
            ['categoria_id' => 1, 'denominacion' => 'Leche entera', 'imagen' => 'leche.png'],
            ['categoria_id' => 1, 'denominacion' => 'Pan integral', 'imagen' => 'pan_integral.png'],
            ['categoria_id' => 1, 'denominacion' => 'Aceite de oliva', 'imagen' => 'aceite.png'],
            ['categoria_id' => 1, 'denominacion' => 'Arroz', 'imagen' => 'arroz.png'],

            // Ropa y Complementos
            ['categoria_id' => 2, 'denominacion' => 'Pantalón corto señora', 'imagen' => 'pantaloncorto.png'],
            ['categoria_id' => 2, 'denominacion' => 'Blusa señora', 'imagen' => 'blusa.png'],
            ['categoria_id' => 2, 'denominacion' => 'Camiseta hombre', 'imagen' => 'camiseta.png'],
            ['categoria_id' => 2, 'denominacion' => 'Chaqueta invierno', 'imagen' => 'chaqueta.png'],
            ['categoria_id' => 2, 'denominacion' => 'Sudadera', 'imagen' => 'sudadera.png'],

            // Peluquería y Estética
            ['categoria_id' => 3, 'denominacion' => 'Champú anti-caspa', 'imagen' => 'caspa.png'],
            ['categoria_id' => 3, 'denominacion' => 'Gel fijador', 'imagen' => 'gel.png'],
            ['categoria_id' => 3, 'denominacion' => 'Secador pelo', 'imagen' => 'secador.png'],
            ['categoria_id' => 3, 'denominacion' => 'Cepillo pelo', 'imagen' => 'cepillo.png'],

            // Ferretería
            ['categoria_id' => 4, 'denominacion' => 'Martillo', 'imagen' => 'martillo.png'],
            ['categoria_id' => 4, 'denominacion' => 'Destornillador', 'imagen' => 'destornillador.png'],
            ['categoria_id' => 4, 'denominacion' => 'Taladro', 'imagen' => 'taladro.png'],
            ['categoria_id' => 4, 'denominacion' => 'Llave inglesa', 'imagen' => 'llave.png'],
            ['categoria_id' => 4, 'denominacion' => 'Sierra manual', 'imagen' => 'sierra.png'],

            // Otros
            ['categoria_id' => 5, 'denominacion' => 'Agua mineral', 'imagen' => 'agua.png'],
            ['categoria_id' => 5, 'denominacion' => 'Refresco cola', 'imagen' => 'cola.png'],
            ['categoria_id' => 5, 'denominacion' => 'Tiritas', 'imagen' => 'tiritas.png'],
            ['categoria_id' => 5, 'denominacion' => 'Pilas AA', 'imagen' => 'pilas.png'],
            ['categoria_id' => 5, 'denominacion' => 'Bombilla LED', 'imagen' => 'bombilla.png'],
        ];

        foreach ($productos as $producto) {
            DB::table('productos')->updateOrInsert(
                [
                    'denominacion' => $producto['denominacion'],
                ],
                [
                    'categoria_id' => $producto['categoria_id'],
                    'imagen'       => $producto['imagen'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]
            );
        }
    }
}
