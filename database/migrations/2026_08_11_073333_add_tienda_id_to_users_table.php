<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de añadir la clave foránea 'tienda_id' a la tabla 'users'
 * para vincular usuarios con una tienda asignada.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración añadiendo la columna 'tienda_id' y su restricción foránea en 'users'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tienda_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('tiendas')
                  ->nullOnDelete();
        });
    }

    /**
     * Revierte la migración eliminando la clave foránea y la columna 'tienda_id' de 'users'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tienda_id']);
            $table->dropColumn('tienda_id');
        });
    }
};
