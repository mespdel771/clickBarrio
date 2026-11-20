<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de añadir borrado (columna deleted_at) a la tabla tiendas
 * para ocultarlas del catálogo al eliminarlas sin romper las reservas históricas.
 */
return new class extends Migration
{
    /**
     * Añade la columna nullable 'deleted_at' a la tabla tiendas.
     */
    public function up(): void
    {
        Schema::table('tiendas', function (Blueprint $table) {
            // Crea la columna timestamp 'deleted_at' utilizada por SoftDeletes
            $table->softDeletes();
        });
    }

    /**
     * Elimina la columna 'deleted_at' si se revierte la migración.
     */
    public function down(): void
    {
        Schema::table('tiendas', function (Blueprint $table) {
            // Revierte el borrado lógico eliminando la columna 'deleted_at'
            $table->dropSoftDeletes();
        });
    }
};
