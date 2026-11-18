{{-- Vista de formulario para editar un producto existente, su imagen, precios y stock por tienda --}}
@extends('layouts.formulario')

@section('contenido')

    {{-- Enlace superior para regresar al listado de productos --}}
    <div class="acciones-tabla">
        <a href="{{ route('productos.index') }}" class="boton-volver">
            ← Volver
        </a>
    </div>

    <h2>Editar producto</h2>

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

    {{-- Formulario de actualización de producto enviado mediante PUT con soporte para archivos --}}
    <form method="POST"
          action="{{ route('productos.update', $producto) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Datos básicos del producto --}}
        <div class="campo-formulario">
            <label>Nombre del producto</label>

            <input type="text"
                   name="denominacion"
                   value="{{ old('denominacion', $producto->denominacion) }}"
                   required>
        </div>

        <div class="campo-formulario">
            <label>Categoría</label>

            <select name="categoria_id" required>
                <option value="">Seleccione una categoría</option>

                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}"
                        {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->denominacion }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Gestión y previsualización de la imagen del producto --}}
        <div class="campo-formulario">
            <label>Imagen del producto</label>

            <input type="file" name="imagen" accept="image/*">

            <small style="display:block; color:gray;">
                Formatos permitidos: JPG, PNG, WEBP (máx 2MB)
            </small>

            @if($producto->imagen)
                <div style="margin-top:10px;">
                    <p>Imagen actual:</p>

                    <img src="{{ asset('storage/' . $producto->imagen) }}"
                         width="150"
                         style="border-radius:8px; border:1px solid #ccc;">
                </div>
            @endif
        </div>

        <hr>

        {{-- Tiendas donde el producto ya se encuentra asignado --}}
        <h2>Tiendas asignadas</h2>

               @foreach($tiendasAsignadas as $tienda)

            @php
                // Recupera el precio y el stock actuales indexados por el ID entero de la tienda
                $precio = $preciosPorTienda->get((int) $tienda->id);
                $stock  = $stocksPorTienda->get((int) $tienda->id);
            @endphp

            <h3>{{ $tienda->nombre }}</h3>

            <div class="campo-formulario">
                <label>
                    <input type="checkbox"
                           name="tiendas[{{ $tienda->id }}][activo]"
                           value="1"
                           checked>
                    Activar en esta tienda
                </label>
            </div>

            <div class="campo-formulario">
                <label>Precio</label>

                <input type="number"
                       step="0.01"
                       min="0"
                       name="tiendas[{{ $tienda->id }}][precio]"
                       value="{{ old("tiendas.$tienda->id.precio", $precio->precio ?? '') }}"
                       placeholder="0.00">
            </div>

            <div class="campo-formulario">
                <label>Stock</label>

                <input type="number"
                       min="0"
                       name="tiendas[{{ $tienda->id }}][stock]"
                       value="{{ old("tiendas.$tienda->id.stock", $stock->stock ?? '') }}"
                       placeholder="0">
            </div>

            <hr>

        @endforeach

        {{-- Tiendas adicionales disponibles para incorporar el producto --}}
        <h2>Tiendas disponibles</h2>

        @foreach($tiendasDisponibles as $tienda)

            <h3>{{ $tienda->nombre }}</h3>

            <div class="campo-formulario">
                <label>
                    <input type="checkbox"
                           name="tiendas[{{ $tienda->id }}][activo]"
                           value="1">
                    Activar en esta tienda
                </label>
            </div>

            <div class="campo-formulario">
                <label>Precio</label>

                <input type="number"
                       step="0.01"
                       min="0"
                       name="tiendas[{{ $tienda->id }}][precio]"
                       value=""
                       placeholder="0.00">
            </div>

            <div class="campo-formulario">
                <label>Stock</label>

                <input type="number"
                       min="0"
                       name="tiendas[{{ $tienda->id }}][stock]"
                       value=""
                       placeholder="0">
            </div>

            <hr>

        @endforeach

        {{-- Botones de acción: actualizar producto o cancelar y volver --}}
        <div class="acciones-tabla">
            <button type="submit" class="boton-guardar">
                Actualizar producto
            </button>

            <a href="{{ route('productos.index') }}" class="boton-volver">
                Cancelar
            </a>
        </div>

    </form>

@endsection
