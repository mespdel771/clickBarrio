<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de crear la tabla 'carrito_items' para almacenar
 * los artículos individuales, cantidades e importes añadidos a cada carrito.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración creando la tabla 'carrito_items'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('carrito_items', function (Blueprint $table) {
            $table->id();

            // Relaciones hacia carrito, tienda y producto.
            $table->foreignId('carrito_id')
                ->constrained('carritos')
                ->onDelete('cascade');

            $table->foreignId('tienda_id')
                ->constrained('tiendas')
                ->onDelete('cascade');

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->onDelete('cascade');

            // Cantidad solicitada, precio unitario e importe de la línea (cantidad * precio).
            $table->integer('cantidad');
            $table->decimal('precio', 10, 2);
            $table->decimal('importe', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Revierte la migración eliminando la tabla 'carrito_items'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('carrito_items');
    }
};
