{{-- Vista de formulario para editar los datos, imagen y gestor asignado de una tienda existente --}}
@extends('layouts.formulario')

@section('contenido')

    <h2>
        Editar tienda
    </h2>

    {{-- Formulario de actualización de tienda enviado por PUT con soporte para subida de archivos --}}
    <form method="POST"
          action="{{ route('tiendas.update', $store) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Campo de nombre comercial de la tienda --}}
        <div class="campo-formulario">
            <label>
                Nombre de la tienda
            </label>

            <input type="text"
                   name="nombre"
                   value="{{ old('nombre', $store->nombre) }}"
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
                   value="{{ old('email', $store->email) }}"
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
                   value="{{ old('persona_contacto', $store->persona_contacto) }}">

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
                   value="{{ old('telefono', $store->telefono) }}">

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
                   value="{{ old('direccion', $store->direccion) }}">

            @error('direccion')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Gestión y previsualización de la imagen actual de la tienda --}}
        <div class="campo-formulario">
            <label>
                Imagen de la tienda
            </label>

            @if($store->imagen)
                <div style="margin-bottom:10px;">
                    <p>
                        Imagen actual:
                    </p>

                    <img src="{{ asset('storage/'.$store->imagen) }}"
                         alt="{{ $store->nombre }}"
                         style="max-width:250px; border-radius:10px;">
                </div>
            @endif

            <input type="file"
                   name="imagen"
                   accept="image/*">

            <small>
                Si no seleccionas una imagen se mantendrá la actual.
            </small>

            @error('imagen')
                <div class="error-formulario">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Selector de gestor asignado (visible únicamente para el rol administrador) --}}
        @if(auth()->user()->hasRole('administrador'))
            <div class="campo-formulario">
                <label>
                    Gestor de la tienda
                </label>

                @if($store->user)
                    <p>
                        👤 Actual:
                        <strong>
                            {{ $store->user->name }}
                        </strong>
                    </p>
                @else
                    <p>
                        ⚠️ Sin gestor asignado
                    </p>
                @endif

                <select name="user_id">
                    <option value="">
                        -- Sin gestor --
                    </option>

                    @foreach($gestores as $usuario)
                        <option value="{{ $usuario->id }}"
                            @selected(old('user_id', $store->user_id) == $usuario->id)>
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
        @endif

        {{-- Botones de acción: cancelar sin guardar o actualizar la tienda --}}
        <div class="botones-formulario">
            <a href="{{ route('tiendas.index') }}" class="boton-volver">
                Cancelar
            </a>

            <button type="submit" class="boton-guardar">
                Actualizar tienda
            </button>
        </div>

    </form>

@endsection
