<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de crear la tabla 'precios' para gestionar el precio
 * asignado a cada producto dentro de cada tienda.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración creando la tabla 'precios'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('precios', function (Blueprint $table) {
            $table->id();

            // Relaciones hacia productos y tiendas.
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->onDelete('cascade');

            $table->foreignId('tienda_id')
                ->constrained('tiendas')
                ->onDelete('cascade');

            // Precio del producto en la tienda (10 dígitos en total, 2 decimales).
            $table->decimal('precio', 10, 2);

            // Evita duplicados: un producto solo puede tener un precio por cada tienda.
            $table->unique([
                'producto_id',
                'tienda_id',
            ]);

            $table->timestamps();
        });
    }

    /**
     * Revierte la migración eliminando la tabla 'precios'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('precios');
    }
};
