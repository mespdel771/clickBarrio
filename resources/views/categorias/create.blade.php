{{-- Vista de formulario para crear una nueva categoría --}}
@extends('layouts.formulario')

@section('contenido')

    <h2>Nueva categoría</h2>

    {{-- Formulario de alta de categoría enviado por POST --}}
    <form method="POST" action="{{ route('categorias.store') }}">
        @csrf

        {{-- Campo de texto para el nombre o denominación de la categoría --}}
        <div class="campo-formulario">
            <label for="denominacion">Denominación</label>

            <input
                type="text"
                id="denominacion"
                name="denominacion"
                value="{{ old('denominacion') }}"
                required>

            {{-- Muestra el mensaje de error de validación si falla este campo --}}
            @error('denominacion')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Botones de acción: volver al listado o guardar la nueva categoría --}}
        <div class="botones-formulario">
            <a href="{{ route('categorias.index') }}" class="boton-cancelar">
                Cancelar
            </a>

            <button type="submit" class="boton-guardar">
                Guardar categoría
            </button>
        </div>
    </form>

@endsection
