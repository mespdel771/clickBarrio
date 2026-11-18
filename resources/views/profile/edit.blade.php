{{-- Vista principal de edición del perfil de usuario (datos personales, cambio de contraseña y eliminación de cuenta) --}}
@extends('layouts.formulario')

@section('contenido')

    {{-- Mensaje de estado tras actualizar perfil o contraseña --}}
    @if (session('status'))
        <div class="mensaje-exito">
            {{ session('status') }}
        </div>
    @endif

    {{-- Formulario de actualización de datos básicos del usuario --}}
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <h2>Datos del usuario</h2>

        {{-- Campo de nombre --}}
        <div class="campo-formulario">
            <label for="name">Nombre</label>
            <input id="name" name="name" type="text"
                   value="{{ old('name', auth()->user()->name) }}" required>
            @error('name')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Campo de correo electrónico --}}
        <div class="campo-formulario">
            <label for="email">Email</label>
            <input id="email" name="email" type="email"
                   value="{{ old('email', auth()->user()->email) }}" required>
            @error('email')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Botón para guardar los cambios de perfil --}}
        <div class="botones-formulario">
            <button class="boton-guardar">
                Guardar cambios
            </button>
        </div>
    </form>

    {{-- Formulario de cambio de contraseña --}}
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <h2 style="margin-top:2rem;">Cambiar contraseña</h2>

        {{-- Contraseña actual --}}
        <div class="campo-formulario">
            <label>Contraseña actual</label>
            <input type="password" name="current_password" required>
            @error('current_password')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Nueva contraseña --}}
        <div class="campo-formulario">
            <label>Nueva contraseña</label>
            <input type="password" name="password" required>
            @error('password')
                <div class="error-formulario">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirmación de la nueva contraseña --}}
        <div class="campo-formulario">
            <label>Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required>
        </div>

        {{-- Botón para actualizar la contraseña --}}
        <div class="botones-formulario">
            <button class="boton-guardar">
                Actualizar contraseña
            </button>
        </div>
    </form>

    {{-- Formulario de eliminación permanente de la cuenta --}}
    <form method="POST" action="{{ route('profile.destroy') }}">
        @csrf
        @method('delete')

        <h2 style="margin-top:2rem; color:#dc2626;">
            Eliminar cuenta
        </h2>

        {{-- Confirmación mediante contraseña actual --}}
        <div class="campo-formulario">
            <label>Confirma tu contraseña</label>
            <input type="password" name="password" required>
        </div>

        {{-- Botón de acción destructiva --}}
        <div class="botones-formulario">
            <button class="boton-eliminar">
                Eliminar cuenta
            </button>
        </div>
    </form>

@endsection
