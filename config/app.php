<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Nombre de la Aplicación
    |--------------------------------------------------------------------------
    |
    | Este valor es el nombre de tu aplicación. El framework lo utilizará
    | cuando necesite mostrar el nombre en notificaciones, correos o
    | cualquier otro elemento de la interfaz de usuario.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Entorno de la Aplicación
    |--------------------------------------------------------------------------
    |
    | Este valor determina el "entorno" en el que se ejecuta la aplicación.
    | Puede influir en cómo prefieres configurar varios servicios que utiliza
    | el sistema. Se recomienda definirlo en tu archivo ".env".
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Modo Depuración (Debug)
    |--------------------------------------------------------------------------
    |
    | Cuando la aplicación está en modo depuración, se mostrarán mensajes de
    | error detallados con el rastreo de la pila en cada fallo. Si se desactiva,
    | se mostrará una página de error genérica y simple.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | URL de la Aplicación
    |--------------------------------------------------------------------------
    |
    | La consola utiliza esta URL para generar correctamente enlaces cuando se
    | ejecuta la herramienta de comandos Artisan. Debes establecer la URL raíz
    | de tu web para que esté disponible en las tareas programadas y comandos.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Zona Horaria
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar la zona horaria por defecto para tu aplicación,
    | la cual usarán las funciones de fecha y hora de PHP. Está configurada
    | en "UTC" por defecto, ideal para la mayoría de los desarrollos.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Configuración de Idioma (Locale)
    |--------------------------------------------------------------------------
    |
    | El idioma de la aplicación determina el lenguaje por defecto que usarán
    | los métodos de traducción y localización de Laravel. Puedes cambiarlo
    | por cualquier idioma para el que tengas carpetas de traducción.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    // Idioma de respaldo si la traducción solicitada no existe
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    // Idioma por defecto para la generación de datos falsos en pruebas (Faker)
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Clave de Encriptación
    |--------------------------------------------------------------------------
    |
    | Esta clave es utilizada por los servicios de encriptación de Laravel y
    | debe ser una cadena aleatoria de 32 caracteres para asegurar que todos
    | los valores cifrados estén protegidos. Haz esto antes de lanzar la web.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    // Historial de claves anteriores para permitir la desencriptación tras una rotación
    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Controlador del Modo Mantenimiento
    |--------------------------------------------------------------------------
    |
    | Estas opciones determinan el controlador (driver) utilizado para gestionar
    | el estado del "modo mantenimiento" de Laravel. El controlador "cache"
    | permite controlar el mantenimiento de forma síncrona en múltiples servidores.
    |
    | Controladores soportados: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
