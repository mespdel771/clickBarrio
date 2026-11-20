<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Reserva;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Servicio encargado de recopilar y preparar las métricas y estadísticas del panel principal (Dashboard)
 * diferenciadas según el rol del usuario (administrador, gestor o usuario estándar).
 */
class DashboardService
{
    /**
     * Obtiene todas las estadísticas globales del sistema para el perfil de administrador,
     * calculando los ingresos al vuelo a partir de las líneas de reserva.
     *
     * @return array<string, mixed>
     */
    public function admin(): array
    {
        return [
            // Número total de tiendas registradas.
            'total_tiendas' => Tienda::count(),

            // Número total de productos registrados.
            'total_productos' => Producto::count(),

            // Número total de reservas realizadas.
            'total_reservas' => Reserva::count(),

            // Número total de usuarios registrados.
            'total_usuarios' => User::count(),

            // Número total de categorías existentes.
            'total_categorias' => Categoria::count(),

            // Suma el importe total de todas las reservas.
            'ingresos_totales' => DB::table('reserva_items')
                ->sum(DB::raw('cantidad * precio')),

            // Consulta los ingresos generados por cada tienda.
            'ingresos_por_tienda' => DB::table('reserva_items')
                ->join('tiendas', 'reserva_items.tienda_id', '=', 'tiendas.id')
                ->select(
                    'tiendas.id',
                    'tiendas.nombre',
                    DB::raw('SUM(reserva_items.cantidad * reserva_items.precio) as total')
                )
                ->groupBy('tiendas.id', 'tiendas.nombre')
                ->orderByDesc('total')
                ->get(),

            // Obtiene los productos que más ingresos han generado.
            'ingresos_por_producto' => DB::table('reserva_items')
                ->join('productos', 'reserva_items.producto_id', '=', 'productos.id')
                ->select(
                    'productos.id',
                    'productos.denominacion',
                    DB::raw('SUM(reserva_items.cantidad * reserva_items.precio) as total')
                )
                ->groupBy('productos.id', 'productos.denominacion')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),

            // Obtiene las 5 tiendas con mayores ingresos.
            'top_tiendas' => DB::table('reserva_items')
                ->join('tiendas', 'reserva_items.tienda_id', '=', 'tiendas.id')
                ->select(
                    'tiendas.id',
                    'tiendas.nombre',
                    DB::raw('SUM(reserva_items.cantidad * reserva_items.precio) as ingresos')
                )
                ->groupBy('tiendas.id', 'tiendas.nombre')
                ->orderByDesc('ingresos')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Obtiene las estadísticas e ingresos correspondientes a las tiendas gestionadas por un usuario gestor.
     *
     * @param int $userId Identificador del usuario gestor.
     * @return array<string, mixed>
     */
    public function gestor(int $userId): array
    {
        // Obtiene los IDs de las tiendas pertenecientes al gestor.
        $tiendas = Tienda::where('user_id', $userId)->pluck('id');

        return [
            // Cuenta cuántas tiendas gestiona el usuario.
            'total_tiendas' => $tiendas->count(),

            // Calcula los ingresos totales de sus tiendas.
            'ingresos_totales' => DB::table('reserva_items')
                ->whereIn('tienda_id', $tiendas)
                ->sum(DB::raw('cantidad * precio')),

            // Obtiene los ingresos generados por cada tienda.
            'ingresos_por_tienda' => DB::table('reserva_items')
                ->whereIn('tienda_id', $tiendas)
                ->join('tiendas', 'reserva_items.tienda_id', '=', 'tiendas.id')
                ->select(
                    'tiendas.id',
                    'tiendas.nombre',
                    DB::raw('SUM(reserva_items.cantidad * reserva_items.precio) as total')
                )
                ->groupBy('tiendas.id', 'tiendas.nombre')
                ->get(),

            // Cuenta las reservas diferentes realizadas en sus tiendas.
            'total_reservas' => DB::table('reserva_items')
                ->whereIn('tienda_id', $tiendas)
                ->distinct('reserva_id')
                ->count('reserva_id'),

            // Obtiene los 5 productos más vendidos en sus tiendas.
            'top_productos' => DB::table('reserva_items')
                ->join('productos', 'reserva_items.producto_id', '=', 'productos.id')
                ->whereIn('tienda_id', $tiendas)
                ->select(
                    'productos.id',
                    'productos.denominacion',
                    DB::raw('SUM(reserva_items.cantidad) as total_vendido')
                )
                ->groupBy('productos.id', 'productos.denominacion')
                ->orderByDesc('total_vendido')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Obtiene el resumen de actividad y últimas reservas realizadas por un usuario estándar.
     *
     * @param int $userId Identificador del usuario cliente.
     * @return array<string, mixed>
     */
    public function usuario(int $userId): array
    {
        return [
            'total_reservas' => Reserva::where('user_id', $userId)->count(),

            'total_gastado' => DB::table('reserva_items')
                ->join('reservas', 'reserva_items.reserva_id', '=', 'reservas.id')
                ->where('reservas.user_id', $userId)
                ->sum(DB::raw('reserva_items.cantidad * reserva_items.precio')),

            'ultimas_reservas' => Reserva::where('user_id', $userId)
                ->with(['items.producto', 'items.tienda'])
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }
}
