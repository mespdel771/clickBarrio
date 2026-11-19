{{-- Vista de formulario para editar los datos, rol, tienda asignada y contraseña opcional de un usuario --}}
@extends('layouts.formulario', [
    'titulo' => 'Editar Usuario'
])

@section('contenido')

    {{-- Formulario de actualización de usuario enviado mediante el método PUT --}}
    <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
        @csrf
        @method('PUT')

        {{-- Campo de nombre completo del usuario --}}
        <div class="campo-formulario">
            <label for="name">Nombre</label>
            <input type="text" id="name" name="name" value="{{ old('name', $usuario->name) }}">
            @error('name')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo de correo electrónico --}}
        <div class="campo-formulario">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}">
            @error('email')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Selector de rol actual del usuario (administrador, gestor o usuario) --}}
        <div class="campo-formulario">
            <label for="rol">Rol</label>
            @php
                // Obtiene el rol previo tras error de validación o el rol actual asignado al usuario
                $rolActual = old('rol', $usuario->getRoleNames()->first());
            @endphp

            <select name="rol" id="rol" onchange="alternarDesplegableTienda()">
                <option value="">Seleccione un rol</option>
                <option value="administrador" {{ $rolActual === 'administrador' ? 'selected' : '' }}>Administrador</option>
                <option value="gestor" {{ $rolActual === 'gestor' ? 'selected' : '' }}>Gestor</option>
                <option value="usuario" {{ $rolActual === 'usuario' ? 'selected' : '' }}>Usuario</option>
            </select>
            @error('rol')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Contenedor dinámico de tienda controlada por JavaScript (visible solo si el rol es 'gestor') --}}
        <div class="campo-formulario" id="contenedor-tienda" style="display: none;">
            <label for="tienda_id">Tienda</label>
            <select name="tienda_id" id="tienda_id">
                <option value="">Seleccione una tienda</option>
                @foreach($tiendas as $tienda)
                    <option value="{{ $tienda->id }}"
                        {{ old('tienda_id', $usuario->tiendas->first()?->id) == $tienda->id ? 'selected' : '' }}>
                        {{ $tienda->nombre }}
                        @if($tienda->user_id === $usuario->id)
                            (Asignada actualmente a este gestor)
                        @elseif($tienda->user)
                            (Gestor actual: {{ $tienda->user->name }} — pasará a usuario)
                        @else
                            (Sin gestor)
                        @endif
                    </option>
                @endforeach
            </select>
            @error('tienda_id')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo opcional para establecer una nueva contraseña --}}
        <div class="campo-formulario">
            <label for="password">Nueva contraseña</label>
            <input type="password" id="password" name="password">
            <small>Dejar en blanco para mantener la contraseña actual.</small>
            @error('password')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Botones de acción: cancelar y volver o guardar los cambios --}}
        <div class="botones-formulario">
            <a href="{{ route('usuarios.index') }}" class="boton-cancelar">Cancelar</a>
            <button type="submit" class="boton-guardar">Guardar Cambios</button>
        </div>
    </form>

    {{-- Script de control visual: muestra el selector de tienda únicamente cuando el rol seleccionado es gestor --}}
    <script>
        function alternarDesplegableTienda() {
            const rolSeleccionado = document.getElementById('rol').value;
            const contenedorTienda = document.getElementById('contenedor-tienda');

            // Muestra el selector de tienda solo si el rol seleccionado es exactamente 'gestor'
            if (rolSeleccionado === 'gestor') {
                contenedorTienda.style.display = 'block';
            } else {
                contenedorTienda.style.display = 'none';
                document.getElementById('tienda_id').value = '';
            }
        }

        // Inicializa la visibilidad del selector de tienda al cargar la vista
        document.addEventListener('DOMContentLoaded', alternarDesplegableTienda);
    </script>

@endsection
