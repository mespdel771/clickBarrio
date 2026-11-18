{{-- Vista de formulario para asignar un nuevo precio de un producto en una tienda --}}
@extends('layouts.formulario')

@section('contenido')

    {{-- Enlace superior para volver al listado de precios del producto --}}
    <div class="acciones-tabla">
        <a href="{{ route('precios.index', $producto) }}" class="boton-volver">
            ← Volver a precios
        </a>
    </div>

    <h2>
        Nuevo precio
    </h2>

    {{-- Mensaje de error personalizado enviado en sesión --}}
    @if(session('error'))
        <div class="mensaje-error">
            {{ session('error') }}
        </div>
    @endif

    {{-- Listado de errores de validación del formulario --}}
    @if($errors->any())
        <div class="mensaje-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Contenedor principal del formulario de creación de precio --}}
    <div class="formulario">
        <form method="POST" action="{{ route('precios.store', $producto) }}">
            @csrf

            {{-- Campo informativo de solo lectura con la denominación del producto --}}
            <div class="campo-formulario">
                <label>
                    Producto
                </label>

                <input type="text" value="{{ $producto->denominacion }}" disabled>
            </div>

            {{-- Selector desplegable con las tiendas disponibles para asignar el precio --}}
            <div class="campo-formulario">
                <label>
                    Tienda
                </label>

                <select name="tienda_id" required>
                    <option value="">
                        Seleccione una tienda
                    </option>

                    @foreach($tiendas as $tienda)
                        <option value="{{ $tienda->id }}" {{ old('tienda_id') == $tienda->id ? 'selected' : '' }}>
                            {{ $tienda->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Campo numérico para introducir el precio unitario en euros --}}
            <div class="campo-formulario">
                <label>
                    Precio (€)
                </label>

                <input
                    type="number"
                    name="precio"
                    step="0.01"
                    min="0"
                    value="{{ old('precio') }}"
                    required>
            </div>

            {{-- Botones de acción: guardar el nuevo precio o cancelar --}}
            <div class="acciones-formulario">
                <button type="submit" class="boton-guardar">
                    Guardar precio
                </button>

                <a href="{{ route('precios.index', $producto) }}" class="boton-volver">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

@endsection
