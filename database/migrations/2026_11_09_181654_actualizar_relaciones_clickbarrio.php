<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de reforzar la integridad referencial (ON DELETE RESTRICT),
 * eliminar columnas redundantes o calculadas ('tienda_id', 'total', 'importe'),
 * definir índices únicos y aplicar restricciones CHECK en precios, stock y cantidades.
 */
return new class extends Migration
{
    /**
     * Ejecuta las modificaciones de integridad, índices únicos y restricciones CHECK en la base de datos.
     *
     * @return void
     */
    public function up(): void
    {
        // 1. Quitar la columna redundante tienda_id en users.
        if (Schema::hasColumn('users', 'tienda_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['tienda_id']);
                $table->dropColumn('tienda_id');
            });
        }

        // 2. Hacer coherente el ON DELETE (RESTRICT) en tiendas y productos, y email obligatorio.
        Schema::table('tiendas', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('restrict');

            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $table->string('email')->nullable(false)->change();
        });

        if (Schema::hasColumn('productos', 'categoria_id')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropForeign(['categoria_id']);
                $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('restrict');
            });
        }

        // 3. Proteger historial en reservas (RESTRICT) y eliminar columna calculada 'total'.
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            if (Schema::hasColumn('reservas', 'total')) {
                $table->dropColumn('total');
            }
        });

        // 4. Vincular reserva_items a precios, eliminar 'importe' y usar RESTRICT coherente.
        Schema::table('reserva_items', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->dropForeign(['tienda_id']);

            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('restrict');
            $table->foreign('tienda_id')->references('id')->on('tiendas')->onDelete('restrict');
            $table->foreign(['producto_id', 'tienda_id'])
                  ->references(['producto_id', 'tienda_id'])
                  ->on('precios')
                  ->onDelete('restrict');

            if (Schema::hasColumn('reserva_items', 'importe')) {
                $table->dropColumn('importe');
            }
        });

        // 5. Un carrito por usuario y evitar duplicados en carrito_items.
        Schema::table('carritos', function (Blueprint $table) {
            $table->unique('user_id');
        });

        Schema::table('carrito_items', function (Blueprint $table) {
            $table->unique(['carrito_id', 'tienda_id', 'producto_id'], 'carrito_items_composite_unique');

            if (Schema::hasColumn('carrito_items', 'importe')) {
                $table->dropColumn('importe');
            }
        });

        // 6. Restricciones CHECK para evitar valores negativos o cero.
        DB::statement('ALTER TABLE precios ADD CONSTRAINT chk_precios_precio CHECK (precio >= 0)');
        DB::statement('ALTER TABLE stocks ADD CONSTRAINT chk_stocks_stock CHECK (stock >= 0)');
        DB::statement('ALTER TABLE reserva_items ADD CONSTRAINT chk_reserva_items_cantidad CHECK (cantidad > 0)');
        DB::statement('ALTER TABLE reserva_items ADD CONSTRAINT chk_reserva_items_precio CHECK (precio >= 0)');

        if (Schema::hasColumn('carrito_items', 'cantidad')) {
            DB::statement('ALTER TABLE carrito_items ADD CONSTRAINT chk_carrito_items_cantidad CHECK (cantidad > 0)');
        }
    }

    /**
     * Revierte las modificaciones de integridad y restricciones aplicadas.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
