{{-- Plantilla base principal (layout) para las vistas autenticadas de la aplicación --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Fuentes tipográficas externas (Figtree desde Bunny Fonts) --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Compilación y carga de estilos y scripts principales mediante Vite --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Pila dinámica para inyectar hojas de estilo específicas desde las vistas hijas --}}
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            {{-- Barra de navegación superior compartida --}}
            @include('layouts.navigation')

            {{-- Encabezado opcional de página cuando se define el slot $header --}}
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Contenido principal inyectado mediante el slot de componente --}}
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
