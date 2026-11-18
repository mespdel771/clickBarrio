{{-- Vista de formulario para crear un nuevo producto y asignar su disponibilidad, precio y stock por tienda --}}
@extends('layouts.formulario')

@section('contenido')

    {{-- Botón superior para regresar al listado de productos --}}
    <div class="acciones-tabla">
        <a href="{{ route('productos.index') }}" class="boton-volver">
            ← Volver
        </a>
    </div>

    <h2>Crear producto</h2>

    {{-- Muestra los errores de validación si el envío del formulario falla --}}
    @if($errors->any())
        <div class="errores">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario de alta de producto con soporte para subida de archivos (imagen) --}}
    <form method="POST"
          action="{{ route('productos.store') }}"
          enctype="multipart/form-data"
          id="form-producto">
        @csrf

        {{-- Campo de denominación o nombre del producto --}}
        <div class="campo-formulario">
            <label for="denominacion">Denominación</label>
            <input type="text"
                   id="denominacion"
                   name="denominacion"
                   value="{{ old('denominacion') }}"
                   minlength="3"
                   maxlength="255"
                   required>
        </div>

        {{-- Selector de categoría a la que pertenece el producto --}}
        <div class="campo-formulario">
            <label for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" required>
                <option value="">Seleccione una categoría</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}"
                        {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->denominacion }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Selector de archivo para la fotografía del producto --}}
        <div class="campo-formulario">
            <label for="imagen">Imagen</label>
            <input type="file" id="imagen" name="imagen" accept="image/png, image/jpeg, image/jpg, image/webp">
        </div>

        <hr>

        {{-- Sección de asignación de precios y stock por establecimiento --}}
        <h2>Tiendas, precios y stock</h2>

        <p>
            Selecciona las tiendas donde estará disponible el producto.
        </p>

        @foreach($tiendas as $tienda)
            {{-- Agrupa cada tienda en un bloque independiente para su validación dinámica --}}
            <div class="bloque-tienda" data-tienda-id="{{ $tienda->id }}">
                
                {{-- Casilla de activación del producto en la tienda --}}
                <div class="campo-formulario">
                    <label>
                        <input type="checkbox"
                               class="check-tienda"
                               name="tiendas[{{ $tienda->id }}][activo]"
                               value="1"
                               {{ old("tiendas.$tienda->id.activo") ? 'checked' : '' }}>
                        <strong>{{ $tienda->nombre }}</strong>
                    </label>
                </div>

                {{-- Precio del producto en esta tienda --}}
                <div class="campo-formulario">
                    <label for="precio_{{ $tienda->id }}">Precio (€)</label>
                    <input type="number"
                           id="precio_{{ $tienda->id }}"
                           class="input-precio"
                           step="0.01"
                           min="0.01"
                           name="tiendas[{{ $tienda->id }}][precio]"
                           value="{{ old("tiendas.$tienda->id.precio") }}"
                           placeholder="0.00">
                </div>

                {{-- Stock inicial disponible en esta tienda --}}
                <div class="campo-formulario">
                    <label for="stock_{{ $tienda->id }}">Stock</label>
                    <input type="number"
                           id="stock_{{ $tienda->id }}"
                           class="input-stock"
                           min="0"
                           step="1"
                           name="tiendas[{{ $tienda->id }}][stock]"
                           value="{{ old("tiendas.$tienda->id.stock") }}"
                           placeholder="0">
                </div>

                <hr>
            </div>
        @endforeach

        {{-- Botones de acción: guardar el producto o cancelar y volver --}}
        <div class="acciones-tabla">
            <button type="submit" class="boton-guardar">
                Guardar producto
            </button>

            <a href="{{ route('productos.index') }}" class="boton-volver">
                Cancelar
            </a>
        </div>

    </form>

    {{-- Script de validación en cliente: exige precio y stock solo en las tiendas marcadas --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bloquesTienda = document.querySelectorAll('.bloque-tienda');

            bloquesTienda.forEach(bloque => {
                const checkbox = bloque.querySelector('.check-tienda');
                const inputPrecio = bloque.querySelector('.input-precio');
                const inputStock = bloque.querySelector('.input-stock');

                // Activa o desactiva el atributo 'required' según el estado del checkbox
                function ajustarValidacion() {
                    if (checkbox.checked) {
                        inputPrecio.setAttribute('required', 'required');
                        inputStock.setAttribute('required', 'required');
                    } else {
                        inputPrecio.removeAttribute('required');
                        inputStock.removeAttribute('required');
                    }
                }

                // Escucha cambios en cada checkbox de tienda
                checkbox.addEventListener('change', ajustarValidacion);
                
                // Ejecuta la comprobación al cargar por si existen valores previos (old)
                ajustarValidacion();
            });
        });
    </script>

@endsection
