<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Componente de vista encargado de gestionar el diseño principal (layout) de la aplicación.
 */
class AppLayout extends Component
{
    /**
     * Obtiene la vista que representa este componente.
     *
     * @return View
     */
    public function render(): View
    {
        // Devuelve la plantilla principal ubicada en resources/views/layouts/app.blade.php.
        return view('layouts.app');
    }
}
