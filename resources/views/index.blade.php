{{-- Vista de bienvenida principal (página de inicio pública) de Las Tiendas del Barrio --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Las Tiendas del Barrio</title>

    {{-- Carga de la hoja de estilos de la pantalla principal mediante Vite --}}
    @vite(['resources/css/principal.css'])
</head>

<body>

    {{-- Tarjeta central de presentación de la plataforma --}}
    <div class="welcome-card">

        {{-- Logotipo vectorial SVG representativo de los comercios locales --}}
        <div class="logo-container">
            <svg
                viewBox="0 0 48 48"
                xmlns="http://www.w3.org/2000/svg"
                aria-label="Logo de Las Tiendas del Barrio"
            >
                {{-- Línea base inferior del suelo --}}
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M2 22h44"
                />

                {{-- Tienda 1 (Izquierda): Alimentación / Verdulería --}}
                <g class="store-grocery">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 22V11m12 11V11M4 11l1.5-3h9l1.5 3M4 11h12"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M10 17a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z M8 15V14"
                    />
                </g>

                {{-- Tienda 2 (Centro): Moda / Boutique --}}
                <g class="store-boutique">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M18 22V8a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14M18 11h12"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M24 14a1.5 1.5 0 0 1 1.5-1.5M21 17h6l-3-3.5-3 3.5Z"
                    />
                </g>

                {{-- Tienda 3 (Derecha): Cafetería / Panadería --}}
                <g class="store-bakery">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M32 22V12m12 11V12M32 12l2-3h8l2 3M32 12h12"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M36 18h3.5a1.5 1.5 0 0 0 1.5-1.5V15h-5v3Z"
                    />
                </g>
            </svg>
        </div>

        {{-- Título principal y subtítulo descriptivo --}}
        <h1>
            Las Tiendas del Barrio a un solo Click
        </h1>

        <p>
            Explora nuestros productos exclusivos.
        </p>

        {{-- Botones de acceso al inicio de sesión y al registro de nuevos usuarios --}}
        <div class="button-group">
            <a href="{{ url('/login') }}" class="btn btn-login">
                Iniciar Sesión
            </a>

            <a href="{{ url('/register') }}" class="btn btn-register">
                Registrarse
            </a>
        </div>

    </div>

</body>

</html>
