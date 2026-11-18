{{-- Vista de formulario para registrar un nuevo usuario con asignación dinámica de tienda para gestores --}}
@extends('layouts.formulario', [
    'titulo' => 'Crear Usuario'
])

@section('contenido')

    {{-- Formulario de alta de usuario enviado por POST --}}
    <form method="POST" action="{{ route('usuarios.store') }}">
        @csrf

        {{-- Campo de nombre completo --}}
        <div class="campo-formulario">
            <label for="name">Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}">
            @error('name')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo de correo electrónico --}}
        <div class="campo-formulario">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
            @error('email')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo de contraseña inicial --}}
        <div class="campo-formulario">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password">
            @error('password')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Selector de rol que activa o desactiva dinámicamente el selector de tienda --}}
        <div class="campo-formulario">
            <label for="rol">Rol</label>
            <select name="rol" id="rol" onchange="alternarDesplegableTienda()">
                <option value="">Seleccione un rol</option>
                <option value="administrador" {{ old('rol') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                <option value="gestor" {{ old('rol') == 'gestor' ? 'selected' : '' }}>Gestor</option>
                <option value="usuario" {{ old('rol') == 'usuario' ? 'selected' : '' }}>Usuario</option>
            </select>
            @error('rol')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Contenedor dinámico de tienda controlada por JavaScript (visible solo si el rol es 'gestor') --}}
        <div class="campo-formulario" id="contenedor-tienda" style="display: none;">
            <label for="tienda_id">Tienda</label>
            <select name="tienda_id" id="tienda_id">
                <option value="">Sin asignar</option>
                @foreach($tiendas as $tienda)
                    <option value="{{ $tienda->id }}" {{ old('tienda_id') == $tienda->id ? 'selected' : '' }}>
                        {{ $tienda->nombre }}
                    </option>
                @endforeach
            </select>
            @error('tienda_id')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Botones de acción: cancelar y volver o guardar el usuario --}}
        <div class="botones-formulario">
            <a href="{{ route('usuarios.index') }}" class="boton-cancelar">Cancelar</a>
            <button type="submit" class="boton-guardar">Guardar Usuario</button>
        </div>
    </form>

    {{-- Script de control visual: muestra el desplegable de tiendas únicamente cuando el rol elegido es gestor --}}
    <script>
        function alternarDesplegableTienda() {
            const rolSeleccionado = document.getElementById('rol').value;
            const contenedorTienda = document.getElementById('contenedor-tienda');

            // Muestra el selector de tienda solo si el rol seleccionado es exactamente 'gestor'
            if (rolSeleccionado === 'gestor') {
                contenedorTienda.style.display = 'block';
            } else {
                contenedorTienda.style.display = 'none';
                document.getElementById('tienda_id').value = ''; // Limpia el valor si se cambia a otro rol
            }
        }

        // Ejecuta la comprobación al cargar la vista por si regresa con errores de validación mediante old()
        document.addEventListener('DOMContentLoaded', alternarDesplegableTienda);
    </script>

@endsection
