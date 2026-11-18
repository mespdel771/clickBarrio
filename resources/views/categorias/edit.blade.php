{{-- Vista de formulario para editar una categoría existente --}}
@extends('layouts.formulario')

@section('contenido')

    <h2>
        Editar categoría
    </h2>

    {{-- Formulario de actualización de categoría enviado mediante el método PUT --}}
    <form method="POST" action="{{ route('categorias.update', $categoria) }}">
        @csrf
        @method('PUT')

        {{-- Campo de texto con la denominación actual de la categoría --}}
        <div class="campo-formulario">
            <label for="denominacion">
                Denominación
            </label>

            <input
                type="text"
                id="denominacion"
                name="denominacion"
                value="{{ old('denominacion', $categoria->denominacion) }}"
                required>

            {{-- Muestra el mensaje de error de validación si falla este campo --}}
            @error('denominacion')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Botones de acción: volver al listado sin guardar o actualizar la categoría --}}
        <div class="botones-formulario">
            <a href="{{ route('categorias.index') }}" class="boton-cancelar">
                Cancelar
            </a>

            <button type="submit" class="boton-guardar">
                Actualizar categoría
            </button>
        </div>
    </form>

@endsection
