<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de crear la tabla 'tiendas' junto con sus relaciones
 * hacia las categorías y los usuarios gestores propietarios.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración creando la tabla 'tiendas'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tiendas', function (Blueprint $table) {
            $table->id();

            // Clave foránea hacia categorías (relación uno a muchos).
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('cascade');

            // Clave foránea hacia usuarios (vincula al gestor con su tienda; nullable por flexibilidad).
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('nombre');
            $table->string('persona_contacto')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Revierte la migración eliminando la tabla 'tiendas'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tiendas');
    }
};
