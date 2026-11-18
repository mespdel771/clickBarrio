<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Componente de vista encargado de gestionar el diseño (layout) de las páginas para usuarios invitados.
 */
class GuestLayout extends Component
{
    /**
     * Obtiene la vista que representa este componente.
     *
     * @return View
     */
    public function render(): View
    {
        // Devuelve la plantilla para usuarios no autenticados.
        return view('layouts.guest');
    }
}
