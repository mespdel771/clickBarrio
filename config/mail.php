<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sistema de Correo (Mailer) Predeterminado
    |--------------------------------------------------------------------------
    |
    | Esta opción controla el sistema de correo por defecto que se utilizará
    | para enviar todos los mensajes, a menos que se especifique otro de forma
    | explícita al realizar el envío. Todos los sistemas adicionales pueden
    | configurarse dentro del arreglo "mailers" incluyendo ejemplos de cada tipo.
    |
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Configuraciones de los Sistemas de Correo (Mailers)
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar todos los sistemas de correo utilizados por tu
    | aplicación junto con sus respectivos ajustes. Se incluyen varios ejemplos
    | ya configurados y puedes añadir los tuyos propios según tus necesidades.
    |
    | Laravel soporta diversos controladores de "transporte" para el envío de
    | correos electrónicos. Puedes especificar cuál utilizas para cada sistema
    | a continuación, así como añadir nuevos sistemas si fuera necesario.
    |
    | Soportados: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |             "postmark", "resend", "log", "array",
    |             "failover", "roundrobin"
    |
    */

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 2525),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'resend' => [
            'transport' => 'resend',
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],

        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
            'retry_after' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Dirección Global de Remitente ("From")
    |--------------------------------------------------------------------------
    |
    | Es posible que desees que todos los correos enviados por tu aplicación
    | salgan desde la misma dirección. Aquí puedes especificar el nombre y la
    | dirección de correo que se utilizarán de forma global en todos los envíos.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', env('APP_NAME', 'Laravel')),
    ],

];
