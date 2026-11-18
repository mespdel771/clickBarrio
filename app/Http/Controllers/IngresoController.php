<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Controlador encargado de gestionar la consulta y cálculo de ingresos de la plataforma.
 */
class IngresoController extends Controller
{
    /**
     * Muestra el resumen de ingresos (totales, por tienda, por producto y por cliente)
     * filtrado según el rol del usuario autenticado:
     * - Administrador: visualiza todos los ingresos globales del sistema.
     * - Gestor: visualiza únicamente los ingresos generados por sus propias tiendas.
     *
     * @return View
     */
    public function index(): View
    {
        // Obtiene el usuario autenticado.
        $user = auth()->user();

        // Administrador puede consultar todos los ingresos
        if ($user->hasRole('administrador')) {

            // Calcula los ingresos totales.
            $totalIngresos = DB::table('reserva_items')
                ->sum(DB::raw('cantidad * precio'));

            // Calcula los ingresos por tiendas.
            $ingresosPorTienda = DB::table('reserva_items')
                ->join('tiendas', 'reserva_items.tienda_id', '=', 'tiendas.id')
                ->select(
                    'tiendas.id',
                    'tiendas.nombre',
                    DB::raw('SUM(reserva_items.cantidad * reserva_items.precio) as total')
                )
                ->groupBy('tiendas.id', 'tiendas.nombre')
                ->orderByDesc('total')
                ->get();

            // Calcula los ingresos por productos.
            $ingresosPorProducto = DB::table('reserva_items')
                ->join('productos', 'reserva_items.producto_id', '=', 'productos.id')
                ->select(
                    'productos.id',
                    'productos.denominacion',
                    DB::raw('SUM(reserva_items.cantidad * reserva_items.precio) as total')
                )
                ->groupBy('productos.id', 'productos.denominacion')
                ->orderByDesc('total')
                ->get();

            // Obtiene los clientes y su gasto total.
            $clientes = DB::table('reserva_items')
                ->join('reservas', 'reserva_items.reserva_id', '=', 'reservas.id')
                ->join('users', 'reservas.user_id', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw('SUM(reserva_items.cantidad * reserva_items.precio) as total_gastado')
                )
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_gastado')
                ->get();

            // Envía los datos a la vista.
            return view('ingresos.index', compact(
                'totalIngresos',
                'ingresosPorTienda',
                'ingresosPorProducto',
                'clientes'
            ));
        }

        // Gestor solo consulta los ingresos de su tienda o tiendas
        if ($user->hasRole('gestor')) {

            $tiendas = DB::table('tiendas')
                ->where('user_id', $user->id)
                ->pluck('id')
                ->toArray();

            $totalIngresos = DB::table('reserva_items')
                ->whereIn('tienda_id', $tiendas)
                ->sum(DB::raw('cantidad * precio'));

            $ingresosPorTienda = DB::table('reserva_items')
                ->join('tiendas', 'reserva_items.tienda_id', '=', 'tiendas.id')
                ->whereIn('reserva_items.tienda_id', $tiendas)
                ->select(
                    'tiendas.id',
                    'tiendas.nombre',
                    DB::raw('SUM(reserva_items.cantidad * reserva_items.precio) as total')
                )
                ->groupBy('tiendas.id', 'tiendas.nombre')
                ->orderByDesc('total')
                ->get();

            $ingresosPorProducto = DB::table('reserva_items')
                ->join('productos', 'reserva_items.producto_id', '=', 'productos.id')
                ->whereIn('reserva_items.tienda_id', $tiendas)
                ->select(
                    'productos.id',
                    'productos.denominacion',
                    DB::raw('SUM(reserva_items.cantidad * reserva_items.precio) as total')
                )
                ->groupBy('productos.id', 'productos.denominacion')
                ->orderByDesc('total')
                ->get();

            // Obtiene clientes con el total gastado en las tiendas seleccionadas.
            $clientes = DB::table('reserva_items')
                ->join('reservas', 'reserva_items.reserva_id', '=', 'reservas.id')
                ->join('users', 'reservas.user_id', '=', 'users.id')
                ->whereIn('reserva_items.tienda_id', $tiendas)
                ->select(
                    'users.id',
                    'users.name',
                    DB::raw('SUM(reserva_items.cantidad * reserva_items.precio) as total_gastado')
                )
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_gastado')
                ->get();

            return view('ingresos.index', compact(
                'totalIngresos',
                'ingresosPorTienda',
                'ingresosPorProducto',
                'clientes'
            ));
        }

        abort(403);
    }
}
