<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de crear la tabla 'reservas' para registrar
 * las compras realizadas por los usuarios junto con sus cantidades e importes.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración creando la tabla 'reservas'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            // Relaciones hacia usuario, tienda y producto.
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tienda_id')->constrained('tiendas')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');

            // Cantidad reservada, precio unitario e importe total (unidades * precio).
            $table->integer('unidades');
            $table->decimal('precio', 10, 2);
            $table->decimal('importe', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Revierte la migración eliminando la tabla 'reservas'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
