<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración de modificación sobre la tabla 'productos'.
 */
return new class extends Migration
{
    /**
     * Ejecuta las modificaciones sobre la tabla 'productos'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            //
        });
    }

    /**
     * Revierte las modificaciones realizadas sobre la tabla 'productos'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            //
        });
    }
};
