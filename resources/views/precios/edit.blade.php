{{-- Vista de formulario para editar el precio asignado a un producto en una tienda --}}
@extends('layouts.formulario')

@section('contenido')

    {{-- Enlace superior para regresar al listado de precios del producto --}}
    <div class="acciones-tabla">
        <a href="{{ route('precios.index', $precio->producto) }}" class="boton-volver">
            ← Volver a precios
        </a>
    </div>

    <h2>
        Editar precio
    </h2>

    {{-- Contenedor principal del formulario de actualización de precio --}}
    <div class="formulario">
        <form method="POST" action="{{ route('precios.update', $precio) }}">
            @csrf
            @method('PUT')

            {{-- Campo informativo de solo lectura con el nombre del producto --}}
            <div class="campo-formulario">
                <label for="producto">
                    Producto
                </label>

                <input type="text" id="producto" value="{{ $precio->producto->denominacion }}" disabled>
            </div>

            {{-- Campo informativo de solo lectura con el nombre de la tienda --}}
            <div class="campo-formulario">
                <label for="tienda">
                    Tienda
                </label>

                <input type="text" id="tienda" value="{{ $precio->tienda->nombre }}" disabled>
            </div>

            {{-- Campo numérico editable con el precio unitario en euros --}}
            <div class="campo-formulario">
                <label for="precio">
                    Precio (€)
                </label>

                <input
                    type="number"
                    name="precio"
                    id="precio"
                    step="0.01"
                    min="0"
                    value="{{ $precio->precio }}"
                    required>
            </div>

            {{-- Botón de envío para guardar el precio actualizado --}}
            <div class="acciones-formulario">
                <button type="submit" class="boton-guardar">
                    Actualizar precio
                </button>
            </div>
        </form>
    </div>

@endsection
