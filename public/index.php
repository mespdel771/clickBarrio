<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Registra la marca de tiempo exacta en la que inicia la ejecución de la petición.
define('LARAVEL_START', microtime(true));

// Comprueba si la aplicación se encuentra en modo mantenimiento y carga la vista correspondiente.
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Registra el cargador automático de clases de Composer (vendor/autoload.php).
require __DIR__.'/../vendor/autoload.php';

// Inicializa la instancia principal de Laravel y procesa la petición HTTP entrante.
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
