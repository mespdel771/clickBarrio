<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de crear la tabla 'productos' junto con su relación
 * hacia las categorías del catálogo.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración creando la tabla 'productos'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            // Clave foránea hacia categorías (elimina los productos si se borra la categoría).
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('cascade');

            $table->string('denominacion');
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Revierte la migración eliminando la tabla 'productos'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
