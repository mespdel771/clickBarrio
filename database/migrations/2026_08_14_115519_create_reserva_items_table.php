<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de crear la tabla 'reserva_items' para almacenar
 * el desglose de productos, cantidades, precios históricos e importes de cada reserva.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración creando la tabla 'reserva_items'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('reserva_items', function (Blueprint $table) {
            $table->id();

            // Relación hacia la reserva principal (elimina las líneas si se borra la reserva).
            $table->foreignId('reserva_id')
                ->constrained('reservas')
                ->cascadeOnDelete();

            // Relación hacia la tienda (restringe el borrado si existen reservas asociadas).
            $table->foreignId('tienda_id')
                ->constrained('tiendas')
                ->restrictOnDelete();

            // Relación hacia el producto (restringe el borrado si existen reservas asociadas).
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            // Cantidad reservada, precio unitario en el momento de la compra e importe total.
            $table->unsignedInteger('cantidad');
            $table->decimal('precio', 10, 2);
            $table->decimal('importe', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Revierte la migración eliminando la tabla 'reserva_items'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva_items');
    }
};
