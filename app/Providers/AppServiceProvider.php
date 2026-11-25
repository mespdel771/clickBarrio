<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

/**
 * Proveedor principal de servicios de la aplicación.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra cualquier servicio o dependencia en el contenedor de Laravel.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa y ejecuta configuraciones globales al arrancar la aplicación.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
