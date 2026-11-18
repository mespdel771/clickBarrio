<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PrecioController;
use App\Http\Controllers\IngresoController;

// Página principal de bienvenida
Route::get('/', function () {
    return view('index');
});

// Panel de control principal (requiere sesión iniciada y correo verificado)
Route::middleware(['auth', 'verified'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

// Gestión del perfil del usuario autenticado
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// Listado público de categorías
Route::get('/categorias', [CategoriaController::class, 'index'])
    ->name('categorias.index');

// Listado público de tiendas
Route::get('/tiendas', [TiendaController::class, 'index'])
    ->name('tiendas.index');

// Creación de nuevas tiendas (exclusivo para administradores)
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/tiendas/create', [TiendaController::class, 'create'])
        ->name('tiendas.create');

    Route::post('/tiendas', [TiendaController::class, 'store'])
        ->name('tiendas.store');
});

// Escaparate y detalle público de una tienda concreta
Route::get('/tiendas/{store}', [TiendaController::class, 'show'])
    ->name('tiendas.show');

// Listado general de productos (requiere autenticación)
Route::middleware('auth')
    ->get('/productos', [ProductoController::class, 'index'])
    ->name('productos.index');

// Historial y detalle individual de reservas/compras
Route::middleware('auth')->group(function () {
    Route::get('/reservas', [ReservaController::class, 'index'])
        ->name('reservas.index');

    Route::get('/reservas/{reserva}', [ReservaController::class, 'show'])
        ->name('reservas.show');
});

// Operaciones del carrito de compras y finalización de pedido
Route::middleware('auth')->group(function () {
    Route::get('/carrito', [CarritoController::class, 'index'])
        ->name('carrito.index');

    Route::post('/carrito/add', [CarritoController::class, 'add'])
        ->name('carrito.add');

    Route::put('/carrito/{item}', [CarritoController::class, 'update'])
        ->name('carrito.update');

    Route::delete('/carrito/{item}', [CarritoController::class, 'destroy'])
        ->name('carrito.destroy');

    Route::post('/carrito/finalizar', [ReservaController::class, 'finalizarCompra'])
        ->name('carrito.finalizar');
});
// Rutas compartidas para los roles Gestor y Administrador
Route::middleware(['auth', 'role:gestor|administrador'])->group(function () {

    // Edición y actualización de tiendas
    Route::get('/tiendas/{store}/edit', [TiendaController::class, 'edit'])
        ->name('tiendas.edit');

    Route::put('/tiendas/{store}', [TiendaController::class, 'update'])
        ->name('tiendas.update');

    // Edición y actualización de productos
    Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])
        ->name('productos.edit');

    Route::put('/productos/{producto}', [ProductoController::class, 'update'])
        ->name('productos.update');
        // Retira un producto de las tiendas del gestor sin borrarlo del catálogo global
    Route::delete('/productos/{producto}/quitar-catalogo', [ProductoController::class, 'removeFromCatalog'])
        ->name('productos.removeFromCatalog');

    // Gestión CRUD de precios por producto y tienda
    Route::get('/productos/{producto}/precios', [PrecioController::class, 'index'])
        ->name('precios.index');

    Route::get('/productos/{producto}/precios/create', [PrecioController::class, 'create'])
        ->name('precios.create');

    Route::post('/productos/{producto}/precios', [PrecioController::class, 'store'])
        ->name('precios.store');

    Route::get('/precios/{precio}/edit', [PrecioController::class, 'edit'])
        ->name('precios.edit');

    Route::put('/precios/{precio}', [PrecioController::class, 'update'])
        ->name('precios.update');

    Route::delete('/precios/{precio}', [PrecioController::class, 'destroy'])
        ->name('precios.destroy');

    // Panel de estadísticas de ingresos
    Route::get('/ingresos', [IngresoController::class, 'index'])
        ->name('ingresos.index');
});

// Incorporación de productos al catálogo por parte de un gestor
Route::middleware(['auth', 'role:gestor'])->group(function () {
    Route::post('/productos/{producto}/add-catalogo', [ProductoController::class, 'addToCatalog'])
        ->name('productos.addToCatalog');
});

// Rutas exclusivas para el rol Administrador
Route::middleware(['auth', 'role:administrador'])->group(function () {

    // Gestión completa CRUD de usuarios
    Route::resource('usuarios', UserController::class);

    // Eliminación de tiendas
    Route::delete('/tiendas/{store}', [TiendaController::class, 'destroy'])
        ->name('tiendas.destroy');

    // Alta y eliminación de productos
    Route::get('/productos/create', [ProductoController::class, 'create'])
        ->name('productos.create');

    Route::post('/productos', [ProductoController::class, 'store'])
        ->name('productos.store');

    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])
        ->name('productos.destroy');

    // Alta, edición y eliminación de categorías
    Route::get('/categorias/create', [CategoriaController::class, 'create'])
        ->name('categorias.create');

    Route::post('/categorias', [CategoriaController::class, 'store'])
        ->name('categorias.store');

    Route::get('/categorias/{categoria}/edit', [CategoriaController::class, 'edit'])
        ->name('categorias.edit');

    Route::put('/categorias/{categoria}', [CategoriaController::class, 'update'])
        ->name('categorias.update');

    Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])
        ->name('categorias.destroy');
});

// Carga de las rutas de autenticación de Laravel Breeze
require __DIR__.'/auth.php';
