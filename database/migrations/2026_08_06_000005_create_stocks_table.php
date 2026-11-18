<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración encargada de crear la tabla 'stocks' para gestionar el inventario
 * de unidades disponibles de cada producto dentro de cada tienda.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración creando la tabla 'stocks'.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            // Relaciones hacia tiendas y productos.
            $table->foreignId('tienda_id')
                ->constrained('tiendas')
                ->onDelete('cascade');

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->onDelete('cascade');

            // Cantidad disponible en inventario (valor por defecto: 20 unidades).
            $table->integer('stock')->default(20);

            // Evita duplicados: un producto solo puede tener un registro de stock por tienda.
            $table->unique([
                'producto_id',
                'tienda_id',
            ]);

            $table->timestamps();
        });
    }

    /**
     * Revierte la migración eliminando la tabla 'stocks'.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
