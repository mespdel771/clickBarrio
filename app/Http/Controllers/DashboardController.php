<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

/**
 * Controlador encargado de gestionar la visualización del panel principal (Dashboard).
 *
 * Determina el rol del usuario autentificado y delega en DashboardService
 * la obtención y preparación de los datos específicos para administradores,
 * gestores o usuarios estándar.
 */
class DashboardController extends Controller
{
    /**
     * Obtiene los datos correspondientes al rol del usuario autentificado y muestra la vista del Dashboard.
     *
     * @param DashboardService $dashboard Servicio encargado de recopilar las métricas y datos del panel.
     * @return View
     */
    public function index(DashboardService $dashboard): View
    {
        $user = auth()->user();

        // Usuario: Administrador
        if ($user->hasRole('administrador')) {
            $data = $dashboard->admin();
        }
        // Usuario: Gestor
        elseif ($user->hasRole('gestor')) {
            $data = $dashboard->gestor($user->id);
        }
        // Usuario: Estándar
        else {
            $data = $dashboard->usuario($user->id);
        }

        return view('dashboard', compact('data'));
    }
}
