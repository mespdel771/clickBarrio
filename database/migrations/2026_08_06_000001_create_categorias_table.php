<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de crear la tabla 'categorias'
 * para clasificar las tiendas y productos del catálogo.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración creando la tabla 'categorias'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('denominacion');
            $table->timestamps();
        });
    }

    /**
     * Revierte la migración eliminando la tabla 'categorias'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
