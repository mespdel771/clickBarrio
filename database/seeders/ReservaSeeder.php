<?php

namespace Database\Seeders;

use App\Models\Precio;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder encargado de poblar las tablas 'reservas' y 'reserva_items'
 * con reservas de prueba coherentes bajo la estructura Cabecera-Detalle sin columnas calculadas.
 */
class ReservaSeeder extends Seeder
{
    /**
     * Ejecuta el sembrado de reservas de prueba generando hasta 15 registros aleatorios
     * a partir de los usuarios con rol cliente y los precios disponibles por tienda.
     *
     * @return void
     */
    public function run(): void
    {
        // 1. Recuperamos los usuarios con rol de cliente común.
        $usuarios = User::role('usuario')->get();

        // 2. Recuperamos los precios existentes para garantizar consistencia relacional (Producto + Tienda).
        $precios = Precio::all();

        if ($usuarios->isEmpty() || $precios->isEmpty()) {
            return;
        }

        // 3. Generamos registros de prueba adaptados a la estructura Cabecera-Detalle sin datos calculados.
        foreach ($precios->random(min(15, $precios->count())) as $precio) {
            $usuario = $usuarios->random();
            $unidades = rand(1, 5);

            // A) Creamos la cabecera en la tabla 'reservas' (sin columna 'total').
            $reserva = Reserva::create([
                'user_id' => $usuario->id,
            ]);

            // B) Insertamos la línea de detalle en 'reserva_items' (sin columna 'importe').
            DB::table('reserva_items')->insert([
                'reserva_id'  => $reserva->id,
                'tienda_id'   => $precio->tienda_id,
                'producto_id' => $precio->producto_id,
                'cantidad'    => $unidades,
                'precio'      => $precio->precio,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}
