<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Disco de Sistema de Archivos Predeterminado
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar el disco del sistema de archivos por defecto que
    | utilizará el framework. El disco "local", así como una variedad de discos
    | basados en la nube, están disponibles para el almacenamiento de archivos.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Discos del Sistema de Archivos
    |--------------------------------------------------------------------------
    |
    | A continuación puedes configurar tantos discos como sean necesarios, e
    | incluso definir múltiples discos para el mismo controlador (driver). Aquí
    | se incluyen ejemplos para la mayoría de los controladores soportados.
    |
    | Controladores soportados: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Enlaces Simbólicos
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar los enlaces simbólicos que se crearán cuando se
    | ejecute el comando de Artisan `storage:link`. Las claves del arreglo deben
    | ser las ubicaciones de los enlaces y los valores sus rutas de destino.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
