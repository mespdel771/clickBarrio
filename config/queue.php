<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nombre de la Conexión de Cola Predeterminada
    |--------------------------------------------------------------------------
    |
    | El sistema de colas de Laravel soporta diversos motores (backends) a través
    | de una única API unificada, permitiéndote acceder cómodamente a cada uno
    | con la misma sintaxis. La conexión de cola por defecto se define abajo.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Conexiones de Colas
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar las opciones de conexión para cada motor de colas
    | utilizado por tu aplicación. Se proporciona una configuración de ejemplo
    | para cada sistema soportado por Laravel, y puedes añadir más libremente.
    |
    | Controladores: "sync", "database", "beanstalkd", "sqs", "redis",
    |                "deferred", "background", "failover", "null"
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'),
            'table' => env('DB_QUEUE_TABLE', 'jobs'),
            'queue' => env('DB_QUEUE', 'default'),
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
            'after_commit' => false,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => env('BEANSTALKD_QUEUE_HOST', 'localhost'),
            'queue' => env('BEANSTALKD_QUEUE', 'default'),
            'retry_after' => (int) env('BEANSTALKD_QUEUE_RETRY_AFTER', 90),
            'block_for' => 0,
            'after_commit' => false,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
            'block_for' => null,
            'after_commit' => false,
        ],

        'deferred' => [
            'driver' => 'deferred',
        ],

        'background' => [
            'driver' => 'background',
        ],

        'failover' => [
            'driver' => 'failover',
            'connections' => [
                'database',
                'deferred',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Procesamiento de Trabajos por Lotes (Job Batching)
    |--------------------------------------------------------------------------
    |
    | Las siguientes opciones configuran la base de datos y la tabla que almacenan
    | la información de los lotes de trabajos. Estos valores pueden actualizarse a
    | cualquier conexión y tabla que hayas definido en tu aplicación.
    |
    */

    'batching' => [
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'job_batches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Trabajos en Cola Fallidos (Failed Queue Jobs)
    |--------------------------------------------------------------------------
    |
    | Estas opciones configuran el registro de los trabajos en cola que fallan,
    | permitiéndote controlar cómo y dónde se almacenan. Laravel incluye soporte
    | para guardar los trabajos fallidos en un archivo simple o en base de datos.
    |
    | Controladores soportados: "database-uuids", "dynamodb", "file", "null"
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'sqlite'),
        'table' => 'failed_jobs',
    ],

];
