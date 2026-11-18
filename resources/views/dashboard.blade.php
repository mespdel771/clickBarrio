{{-- Vista principal del Panel de Control (Dashboard) con accesos directos y métricas según el rol --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel de Control - Las Tiendas del Barrio</title>

    {{-- Carga de estilos principales compilados con Vite --}}
    @vite(['resources/css/app.css'])

    <style>
        /* Cuadrícula adaptable para las tarjetas principales de acceso rápido */
        .grupo-botones-principales {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .boton-principal {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            min-height: 120px;
        }

        .boton-principal .titulo {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .boton-principal .numero {
            font-size: 26px;
            font-weight: bold;
        }

        .boton-tiendas { background: #3498db; }
        .boton-productos { background: #2ecc71; }
        .boton-reservas { background: #e67e22; }
        .boton-categorias { background: #9b59b6; }
        .boton-usuarios { background: #e74c3c; }

        /* Estilos del logotipo vectorial SVG en la cabecera */
        .logo-container {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-container svg {
            width: 80px;
            height: 80px;
            fill: none;
            stroke: #4f46e5;
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .logo-linea {
            stroke: #4f46e5;
        }

        .store-grocery {
            stroke: #10b981;
        }

        .store-boutique {
            stroke: #8b5cf6;
        }

        .store-bakery {
            stroke: #f59e0b;
        }

        .nombre-proyecto {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1f2937;
        }
    </style>
</head>

<body>

<div class="panel-container">

    {{-- Barra superior con logotipo, título del panel, perfil y cierre de sesión --}}
    <header class="barra-superior">

        <div class="cabecera-izquierda">
            <div class="logo-container">
                <svg viewBox="0 0 48 48" xmlns="http://w3.org">
                    <path class="logo-linea" d="M2 22h44" />

                    <g class="store-grocery">
                        <path d="M4 22V11m12 11V11M4 11l1.5-3h9l1.5 3M4 11h12" />
                    </g>

                    <g class="store-boutique">
                        <path d="M18 22V8a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14M18 11h12" />
                    </g>

                    <g class="store-bakery">
                        <path d="M32 22V12m12 11V12M32 12l2-3h8l2 3M32 12h12" />
                    </g>
                </svg>
            </div>

            <span class="nombre-proyecto">
                Las Tiendas del Barrio
            </span>
        </div>

        <h1 class="titulo-principal">
            Panel de Control
        </h1>

        <div class="acciones-usuario">
            <a href="{{ route('profile.edit') }}" class="boton-secundario boton-perfil">
                👤 Perfil
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button class="boton-secundario boton-salir">
                    🚪 Salir
                </button>
            </form>
        </div>

    </header>

    @php
        // Obtiene el usuario autenticado y su rol principal (por defecto 'usuario')
        $user = auth()->user();
        $role = $user?->getRoleNames()->first() ?? 'usuario';
    @endphp
    {{-- PANEL DE CONTROL PARA ADMINISTRADOR (con contadores globales de todas las entidades) --}}
    @if($role === 'administrador')

        <div class="grupo-botones-principales">
            <a href="{{ route('tiendas.index') }}" class="boton-principal boton-tiendas">
                <span class="titulo">Tiendas</span>
                <span class="numero">{{ $data['total_tiendas'] ?? 0 }}</span>
            </a>

            <a href="{{ route('productos.index') }}" class="boton-principal boton-productos">
                <span class="titulo">Productos</span>
                <span class="numero">{{ $data['total_productos'] ?? 0 }}</span>
            </a>

            <a href="{{ route('reservas.index') }}" class="boton-principal boton-reservas">
                <span class="titulo">Reservas</span>
                <span class="numero">{{ $data['total_reservas'] ?? 0 }}</span>
            </a>

            <a href="{{ route('categorias.index') }}" class="boton-principal boton-categorias">
                <span class="titulo">Categorías</span>
                <span class="numero">{{ $data['total_categorias'] ?? 0 }}</span>
            </a>

            <a href="{{ route('ingresos.index') }}" class="boton-principal boton-categorias">
                <span class="titulo">Ingresos</span>
                <span class="numero">{{ number_format($data['ingresos_totales'] ?? 0, 2) }} €</span>
            </a>

            <a href="{{ route('usuarios.index') }}" class="boton-principal boton-usuarios">
                <span class="titulo">Usuarios</span>
                <span class="numero">{{ $data['total_usuarios'] ?? 0 }}</span>
            </a>
        </div>

    {{-- PANEL DE CONTROL PARA GESTOR (acceso a sus tiendas, productos, reservas e ingresos) --}}
    @elseif($role === 'gestor')

        <div class="grupo-botones-principales">
            <a href="{{ route('tiendas.index') }}" class="boton-principal boton-tiendas">
                <span class="titulo">Mis tiendas</span>
            </a>

            <a href="{{ route('productos.index') }}" class="boton-principal boton-productos">
                <span class="titulo">Productos</span>
            </a>

            <a href="{{ route('reservas.index') }}" class="boton-principal boton-reservas">
                <span class="titulo">Reservas</span>
            </a>

            <a href="{{ route('ingresos.index') }}" class="boton-principal boton-categorias">
                <span class="titulo">Ingresos</span>
                <span class="numero">{{ number_format($data['ingresos_totales'] ?? 0, 2) }} €</span>
            </a>
        </div>

    {{-- PANEL DE CONTROL PARA USUARIO CLIENTE (exploración del catálogo y sus reservas) --}}
    @else

        <div class="grupo-botones-principales">
            <a href="{{ route('tiendas.index') }}" class="boton-principal boton-tiendas">
                <span class="titulo">Tiendas</span>
            </a>

            <a href="{{ route('productos.index') }}" class="boton-principal boton-productos">
                <span class="titulo">Productos</span>
            </a>

            <a href="{{ route('categorias.index') }}" class="boton-principal boton-categorias">
                <span class="titulo">Categorías</span>
            </a>

            <a href="{{ route('reservas.index') }}" class="boton-principal boton-reservas">
                <span class="titulo">Mis Reservas</span>
            </a>
        </div>

    @endif

    {{-- Tarjeta informativa inferior indicando el rol activo del usuario --}}
    <div class="tarjeta-informacion">
        <p>
            Rol actual:
            <strong>
                {{ ucfirst($role) }}
            </strong>
        </p>
    </div>

</div>

</body>

</html>
