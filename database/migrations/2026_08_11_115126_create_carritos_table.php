<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de crear la tabla 'carritos' para almacenar
 * el carrito de compra activo asociado a cada usuario.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración creando la tabla 'carritos'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('carritos', function (Blueprint $table) {
            $table->id();

            // Clave foránea hacia usuarios (elimina el carrito si se borra el usuario).
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Revierte la migración eliminando la tabla 'carritos'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('carritos');
    }
};
