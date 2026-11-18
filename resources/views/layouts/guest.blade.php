{{-- Plantilla base (layout) para vistas públicas y de autenticación (login, registro, recuperación) --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ config('app.name', 'Laravel') }}
    </title>

    {{-- Fuentes tipográficas externas (Figtree desde Bunny Fonts) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Compilación y carga de estilos de autenticación y scripts principales mediante Vite --}}
    @vite([
        'resources/css/auth.css',
        'resources/js/app.js'
    ])
</head>

<body>

    {{-- Contenedor principal centrado para formularios de acceso --}}
    <div class="contenedor-auth">

        {{-- Logotipo de la aplicación enlazado a la página de inicio --}}
        <div class="logo-auth">
            <a href="/">
                <x-application-logo />
            </a>
        </div>

        {{-- Tarjeta donde se inyecta el formulario de cada vista hija --}}
        <div class="tarjeta-auth-contenedor">
            {{ $slot }}
        </div>

    </div>

</body>

</html>
