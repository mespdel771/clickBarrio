{{-- Plantilla base (layout) para vistas de gestión, listados y formularios --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    {{-- Token CSRF para peticiones AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ $titulo ?? 'Gestión' }}
    </title>

    {{-- Hoja de estilos específica de formularios y tablas --}}
    <link rel="stylesheet" href="{{ asset('css/formulario.css') }}">

    {{-- Permite inyectar hojas de estilo adicionales desde las vistas hijas --}}
    @stack('styles')
</head>

<body>

<div class="contenedor-formulario">

    {{-- Cabecera principal --}}
    <div class="cabecera-formulario">

        {{-- Zona izquierda: logotipo  y nombre del proyecto --}}
        <div class="cabecera-izquierda">
            <div class="logo-container">
                
                <img src="{{ asset('imagen/logo.png') }}" alt="Logotipo Las Tiendas del Barrio" 
                 style="width: 180px !important; height: 80px !important; display: block; object-fit: contain;">

            </div>

            <span class="nombre-proyecto">
                Las Tiendas del Barrio
            </span>
        </div>

        {{-- Título dinámico de la vista actual --}}
        <h1 class="titulo-formulario">
            {{ $titulo ?? 'Gestión' }}
        </h1>
        {{-- Zona derecha: acceso al carrito y enlace al panel principal --}}
        <div class="cabecera-acciones">

            {{-- Acceso directo al carrito (solo usuarios con rol cliente) --}}
            @auth
                @if(auth()->user()->hasRole('usuario'))
                    <a href="{{ route('carrito.index') }}" class="boton-carrito">
                        🛒
                        <span>
                            Carrito
                        </span>
                    </a>
                @endif
            @endauth

            {{-- Enlace directo al Panel de Control (evita bucles en el historial del navegador) --}}
            <a href="{{ route('dashboard') }}" class="boton-volver">
                ← Panel de Control
            </a>
        </div>

    </div>


    {{-- Tarjeta de contenido principal --}}
    <div class="tarjeta-formulario">

        {{-- Mensaje de éxito en sesión --}}
        @if(session('success'))
            <div class="mensaje-exito">
                {{ session('success') }}
            </div>
        @endif

        {{-- Mensaje de error en sesión --}}
        @if(session('error'))
            <div class="mensaje-error">
                {{ session('error') }}
            </div>
        @endif

        {{-- Listado de errores de validación de formularios --}}
        @if($errors->any())
            <div class="mensaje-error">
                <strong>
                    Se han encontrado los siguientes errores:
                </strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Sección dinámica donde se inyecta el contenido de cada vista hija --}}
        @yield('contenido')

    </div>

</div>

{{-- Modal de confirmación para eliminar registros --}}
<div id="modalEliminar"
     style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,.5);
        align-items:center;
        justify-content:center;
        z-index:9999;
     ">

    <div style="
        background:white;
        padding:30px;
        border-radius:12px;
        width:350px;
        max-width:90%;
        text-align:center;
     ">

        <h3>
            Confirmar eliminación
        </h3>

        <p>
            ¿Seguro que quieres eliminar este elemento?
        </p>

        <br>

        <button type="button" onclick="cerrarModalEliminar()">
            Cancelar
        </button>

        <button type="button" onclick="confirmarEliminar()">
            Eliminar
        </button>

    </div>

</div>

{{-- Lógica JavaScript para el control del modal de eliminación --}}
<script>
    let formularioEliminar = null;

    // Abre el modal y guarda la referencia al formulario del botón pulsado
    function abrirModalEliminar(boton) {
        formularioEliminar = boton.closest('form');
        document.getElementById('modalEliminar').style.display = 'flex';
    }

    // Cierra el modal y limpia la referencia al formulario
    function cerrarModalEliminar() {
        document.getElementById('modalEliminar').style.display = 'none';
        formularioEliminar = null;
    }

    // Envía el formulario guardado para ejecutar la eliminación
    function confirmarEliminar() {
        if (formularioEliminar) {
            formularioEliminar.submit();
        }
    }

    // Cierra el modal si el usuario hace clic fuera del cuadro central
    document.getElementById('modalEliminar').addEventListener('click', function (event) {
        if (event.target === this) {
            cerrarModalEliminar();
        }
    });
</script>

</body>

</html>
