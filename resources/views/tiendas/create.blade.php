{{-- Vista de formulario para registrar una nueva tienda en el sistema --}}
@extends('layouts.formulario')

@section('contenido')

    <h2>
        Crear nueva tienda
    </h2>

    {{-- Formulario de alta de tienda con soporte para subida de imagen --}}
    <form method="POST"
          action="{{ route('tiendas.store') }}"
          enctype="multipart/form-data">
        @csrf

        {{-- Campo de nombre comercial de la tienda --}}
        <div class="campo-formulario">
            <label>
                Nombre de la tienda
            </label>

            <input type="text"
                   name="nombre"
                   value="{{ old('nombre') }}"
                   required>

            @error('nombre')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Campo de correo electrónico de contacto de la tienda --}}
        <div class="campo-formulario">
            <label>
                Correo electrónico
            </label>

            <input type="email"
                   name="email"
                   value="{{ old('email') }}"
                   required>

            @error('email')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Campo opcional de persona de contacto --}}
        <div class="campo-formulario">
            <label>
                Persona de contacto
            </label>

            <input type="text"
                   name="persona_contacto"
                   value="{{ old('persona_contacto') }}">

            @error('persona_contacto')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Campo opcional de teléfono de contacto --}}
        <div class="campo-formulario">
            <label>
                Teléfono
            </label>

            <input type="text"
                   name="telefono"
                   value="{{ old('telefono') }}">

            @error('telefono')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Campo de dirección física del establecimiento --}}
        <div class="campo-formulario">
            <label>
                Dirección
            </label>

            <input type="text"
                   name="direccion"
                   value="{{ old('direccion') }}">

            @error('direccion')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Campo de carga de fotografía o logotipo de la tienda --}}
        <div class="campo-formulario">
            <label>
                Imagen de la tienda
            </label>

            <input type="file"
                   name="imagen"
                   accept="image/*">

            @error('imagen')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Selector de categoría a la que pertenece la tienda --}}
        <div class="campo-formulario">
            <label>
                Categoría
            </label>

            <select name="categoria_id" required>
                <option value="">
                    Seleccione categoría
                </option>

                @foreach(\App\Models\Categoria::orderBy('denominacion')->get() as $categoria)
                    <option value="{{ $categoria->id }}"
                        @if(old('categoria_id') == $categoria->id) selected @endif>
                        {{ $categoria->denominacion }}
                    </option>
                @endforeach
            </select>

            @error('categoria_id')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Selector opcional del usuario con rol gestor responsable de la tienda --}}
        <div class="campo-formulario">
            <label>
                Gestor asignado
            </label>

            <select name="user_id">
                <option value="">
                    Sin gestor
                </option>

                @foreach($gestores as $usuario)
                    <option value="{{ $usuario->id }}"
                        @if(old('user_id') == $usuario->id) selected @endif>
                        {{ $usuario->name }}
                    </option>
                @endforeach
            </select>

            @error('user_id')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Botones de acción: cancelar y volver o guardar la nueva tienda --}}
        <div class="botones-formulario">
            <a href="{{ route('tiendas.index') }}" class="boton-volver">
                Cancelar
            </a>

            <button type="submit" class="boton-guardar">
                Guardar tienda
            </button>
        </div>

    </form>

@endsection
