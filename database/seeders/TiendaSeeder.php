<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder encargado de rellenar la tabla 'tiendas' con los establecimientos iniciales
 * distribuidos entre las cinco categorías del sistema.
 */
class TiendaSeeder extends Seeder
{
    /**
     * Ejecuta el rellenado de datos insertando o actualizando las tiendas base.
     *
     * @return void
     */
    public function run(): void
    {
        $tiendas = [
            // Alimentación
            [
                'categoria_id'     => 1,
                'nombre'           => 'Alimentación Juan',
                'persona_contacto' => 'Juan Pérez',
                'telefono'         => '600123456',
                'email'            => 'juan@alimentacion.com',
                'direccion'        => 'Calle Gran Vía 12',
            ],
            [
                'categoria_id'     => 1,
                'nombre'           => 'Alimentación Sara',
                'persona_contacto' => 'Sara Luz',
                'telefono'         => '600343456',
                'email'            => 'sara@alimentacion.com',
                'direccion'        => 'Calle Sol 2',
            ],
            [
                'categoria_id'     => 1,
                'nombre'           => 'Supermercado López',
                'persona_contacto' => 'Carlos López',
                'telefono'         => '601111111',
                'email'            => 'carlos@superlopez.com',
                'direccion'        => 'Calle Mayor 10',
            ],
            [
                'categoria_id'     => 1,
                'nombre'           => 'MiniMarket Express',
                'persona_contacto' => 'Ana Ruiz',
                'telefono'         => '602222222',
                'email'            => 'ana@minimarket.com',
                'direccion'        => 'Calle Nueva 5',
            ],

            // Ropa y Complementos
            [
                'categoria_id'     => 2,
                'nombre'           => 'Modas Paz',
                'persona_contacto' => 'Luisa Sanz',
                'telefono'         => '600671456',
                'email'            => 'luisa@ropa.com',
                'direccion'        => 'Calle Vía 34',
            ],
            [
                'categoria_id'     => 2,
                'nombre'           => 'Modas Loyma',
                'persona_contacto' => 'Rosa Sanz',
                'telefono'         => '600321456',
                'email'            => 'rosa@ropa.com',
                'direccion'        => 'Calle Luz 2',
            ],
            [
                'categoria_id'     => 2,
                'nombre'           => 'Boutique Elegance',
                'persona_contacto' => 'Marta Gil',
                'telefono'         => '603333333',
                'email'            => 'marta@elegance.com',
                'direccion'        => 'Avenida Moda 8',
            ],
            [
                'categoria_id'     => 2,
                'nombre'           => 'Ropa Urbana',
                'persona_contacto' => 'Luis Vega',
                'telefono'         => '604444444',
                'email'            => 'luis@urbana.com',
                'direccion'        => 'Calle Trend 21',
            ],

            // Peluquería y Estética
            [
                'categoria_id'     => 3,
                'nombre'           => 'Peluquería Marina',
                'persona_contacto' => 'Marina',
                'telefono'         => '600123222',
                'email'            => 'marina@pelu.com',
                'direccion'        => 'Calle Sol 32',
            ],
            [
                'categoria_id'     => 3,
                'nombre'           => 'Peluquería Ángel',
                'persona_contacto' => 'Ángel Luis',
                'telefono'         => '603123222',
                'email'            => 'angel@pelu.com',
                'direccion'        => 'Calle Luna 9',
            ],
            [
                'categoria_id'     => 3,
                'nombre'           => 'Estética Bella',
                'persona_contacto' => 'Laura Gómez',
                'telefono'         => '605555555',
                'email'            => 'laura@bella.com',
                'direccion'        => 'Calle Belleza 3',
            ],
            [
                'categoria_id'     => 3,
                'nombre'           => 'Hair Studio Pro',
                'persona_contacto' => 'David Ruiz',
                'telefono'         => '606666666',
                'email'            => 'david@hairpro.com',
                'direccion'        => 'Avenida Estilo 7',
            ],

            // Ferretería
            [
                'categoria_id'     => 4,
                'nombre'           => 'Ferretería Tornillo',
                'persona_contacto' => 'José Luis',
                'telefono'         => '600563456',
                'email'            => 'jose@ferro.com',
                'direccion'        => 'Avenida Conde 2',
            ],
            [
                'categoria_id'     => 4,
                'nombre'           => 'Ferretería Sanabria',
                'persona_contacto' => 'María',
                'telefono'         => '608963456',
                'email'            => 'maria@ferro.com',
                'direccion'        => 'Avenida Lumbreras 12',
            ],
            [
                'categoria_id'     => 4,
                'nombre'           => 'Herramientas Pro',
                'persona_contacto' => 'Antonio Pérez',
                'telefono'         => '607777777',
                'email'            => 'antonio@herramientas.com',
                'direccion'        => 'Calle Industria 11',
            ],
            [
                'categoria_id'     => 4,
                'nombre'           => 'Brico Center',
                'persona_contacto' => 'Raúl Sánchez',
                'telefono'         => '608888888',
                'email'            => 'raul@brico.com',
                'direccion'        => 'Polígono Norte 5',
            ],

            // Otros
            [
                'categoria_id'     => 5,
                'nombre'           => 'El Desavio',
                'persona_contacto' => 'Luisa López',
                'telefono'         => '604123456',
                'email'            => 'luisa@desavio.com',
                'direccion'        => 'Calle Retama 3',
            ],
            [
                'categoria_id'     => 5,
                'nombre'           => 'Farmacia Tirita',
                'persona_contacto' => 'Tomás García',
                'telefono'         => '606723456',
                'email'            => 'tomas@farmacia.com',
                'direccion'        => 'Calle Natividad 43',
            ],
            [
                'categoria_id'     => 5,
                'nombre'           => 'Papelería Central',
                'persona_contacto' => 'Elena Torres',
                'telefono'         => '609999999',
                'email'            => 'elena@papeleria.com',
                'direccion'        => 'Calle Colegio 14',
            ],
            [
                'categoria_id'     => 5,
                'nombre'           => 'ElectroHogar',
                'persona_contacto' => 'Miguel Ángel',
                'telefono'         => '610000000',
                'email'            => 'miguel@electro.com',
                'direccion'        => 'Avenida Tecnología 6',
            ],
        ];

        foreach ($tiendas as $tienda) {
            DB::table('tiendas')->updateOrInsert(
                [
                    'nombre' => $tienda['nombre'],
                ],
                [
                    'categoria_id'     => $tienda['categoria_id'],
                    'user_id'          => null,
                    'persona_contacto' => $tienda['persona_contacto'],
                    'telefono'         => $tienda['telefono'],
                    'email'            => $tienda['email'],
                    'direccion'        => $tienda['direccion'],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            );
        }
    }
}
