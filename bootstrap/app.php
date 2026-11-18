<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Inicia la configuración de la aplicación indicando su directorio base.
return Application::configure(basePath: dirname(__DIR__))
    // Configura las rutas principales de la aplicación.
    ->withRouting(
        // Define el archivo donde se encuentran las rutas web.
        web: __DIR__.'/../routes/web.php',
        // Define el archivo donde se encuentran los comandos de consola.
        commands: __DIR__.'/../routes/console.php',
        // Define la ruta utilizada para comprobar que la aplicación está funcionando.
        health: '/up',
    )
    // Configura los middleware disponibles en la aplicación.
    ->withMiddleware(function (Middleware $middleware): void {
        // Registra alias personalizados para utilizar los middleware mediante un nombre corto.
        $middleware->alias([
            // Permite proteger rutas según el rol del usuario.
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            // Permite proteger rutas según un permiso concreto.
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            // Permite comprobar si el usuario tiene un rol o un permiso.
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            // Comprueba que el usuario sea propietario del recurso solicitado.
            'owner' => \App\Http\Middleware\CheckOwnership::class,
        ]);
    })
    // Configura el tratamiento de las excepciones de la aplicación.
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    // Finaliza la configuración y crea la aplicación Laravel.
    ->create();
