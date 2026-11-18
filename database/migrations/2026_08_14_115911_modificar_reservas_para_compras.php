<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de refactorizar la tabla 'reservas' en una cabecera de compra,
 * eliminando el detalle unitario anterior y añadiendo el campo 'total'.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración eliminando las columnas de detalle antiguas y añadiendo el total de la compra.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            // Elimina las claves foráneas antiguas de tienda y producto.
            $table->dropForeign(['tienda_id']);
            $table->dropForeign(['producto_id']);

            // Elimina las columnas individuales desplazadas a 'reserva_items'.
            $table->dropColumn([
                'tienda_id',
                'producto_id',
                'unidades',
                'precio',
                'importe',
            ]);

            // Añade el importe total consolidado de la reserva.
            $table->decimal('total', 10, 2)
                ->default(0)
                ->after('user_id');
        });
    }

    /**
     * Revierte la migración eliminando 'total' y restaurando las columnas y relaciones anteriores.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            // Elimina el importe total consolidado.
            $table->dropColumn('total');

            // Restaura las columnas y claves foráneas originales.
            $table->foreignId('tienda_id')
                ->constrained('tiendas')
                ->cascadeOnDelete();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();

            $table->integer('unidades');
            $table->decimal('precio', 10, 2);
            $table->decimal('importe', 10, 2);
        });
    }
};
