<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Valores Predeterminados de Autenticación
    |--------------------------------------------------------------------------
    |
    | Esta opción define el sistema de autenticación ("guard") y el gestor de
    | restablecimiento de contraseñas ("broker") por defecto de tu aplicación.
    | Puedes cambiar estos valores según sea necesario, aunque son un punto de
    | partida ideal para la mayoría de las aplicaciones.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Guardias de Autenticación (Authentication Guards)
    |--------------------------------------------------------------------------
    |
    | A continuación, puedes definir cada guardia de autenticación de tu aplicación.
    | Por supuesto, ya se ha definido una excelente configuración por defecto que
    | utiliza el almacenamiento de sesión y el proveedor de usuarios Eloquent.
    |
    | Todos los guardias de autenticación tienen un proveedor de usuarios, que
    | define cómo se recuperan realmente los usuarios de la base de datos u otro
    | sistema de almacenamiento utilizado. Normalmente se utiliza Eloquent.
    |
    | Soportados: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Proveedores de Usuarios
    |--------------------------------------------------------------------------
    |
    | Todos los guardias de autenticación tienen un proveedor de usuarios, que
    | define cómo se recuperan realmente los usuarios de la base de datos u otro
    | sistema de almacenamiento utilizado. Normalmente se utiliza Eloquent.
    |
    | Si tienes múltiples tablas o modelos de usuarios, puedes configurar varios
    | proveedores para representar cada modelo o tabla. Estos proveedores pueden
    | asignarse posteriormente a cualquier guardia adicional que hayas definido.
    |
    | Soportados: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Restablecimiento de Contraseñas
    |--------------------------------------------------------------------------
    |
    | Estas opciones de configuración especifican el comportamiento de la función
    | de restablecimiento de contraseñas de Laravel, incluyendo la tabla utilizada
    | para almacenar los tokens y el proveedor de usuarios que los recupera.
    |
    | El tiempo de expiración ("expire") es el número de minutos durante los cuales
    | cada token de restablecimiento se considerará válido. Esta medida de seguridad
    | mantiene los tokens activos poco tiempo para reducir el riesgo de ataques.
    |
    | El ajuste "throttle" es el número de segundos que un usuario debe esperar
    | antes de generar más tokens de restablecimiento, evitando solicitudes masivas.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tiempo de Espera de Confirmación de Contraseña
    |--------------------------------------------------------------------------
    |
    | Aquí puedes definir el número de segundos antes de que expire una ventana
    | de confirmación de contraseña y se vuelva a pedir al usuario que introduzca
    | su contraseña en la pantalla de confirmación. Por defecto dura tres horas.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
