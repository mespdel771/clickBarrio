{{-- Vista principal del carrito de compras agrupado por tienda con actualización y borrado vía AJAX --}}
@extends('layouts.formulario')

@push('styles')
    {{-- Carga la hoja de estilos externa del carrito --}}
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
@endpush

@section('contenido')

    {{-- Enlace para regresar al catálogo de productos --}}
    <div class="acciones-tabla">
        <a href="{{ route('productos.index') }}"
           class="boton-volver">
            ← Seguir reservando
        </a>
    </div>

    <h2>
        Mi carrito
    </h2>

    {{-- Contenedores de alertas dinámicas para respuestas AJAX --}}
    <div id="mensaje-carrito"
         class="alert alert-success"
         style="display:none;">
    </div>

    <div id="mensaje-carrito-error"
         class="alert alert-danger"
         style="display:none;">
    </div>

    {{-- Alertas de sesión estándar enviadas desde Laravel --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Comprueba si el carrito no contiene artículos --}}
    @if($carrito->items->isEmpty())

        <div class="carrito-vacio">
            <h3>
                Tu carrito está vacío
            </h3>

            <p>
                Todavía no has añadido ningún producto.
            </p>

            <br>

            <a href="{{ route('productos.index') }}"
               class="boton-guardar">
                Ver productos
            </a>
        </div>

    @else

        {{-- Contenedor principal de artículos agrupados por tienda --}}
        <div class="carrito-contenedor">

            @php
                // Inicializa el total general y agrupa las líneas del carrito por tienda
                $total = 0;
                $agrupado = $carrito->items->groupBy('tienda_id');
            @endphp

            {{-- Recorre cada tienda que tiene productos en el carrito --}}
            @foreach($agrupado as $tiendaId => $items)

                @php
                    $tienda = $items->first()->tienda;
                    $subtotalTienda = 0;
                @endphp

                {{-- Encabezado con el nombre de la tienda --}}
                <h3 style="margin-top:30px;">
                    🏪 {{ $tienda?->nombre ?? 'Tienda no disponible' }}
                </h3>

                {{-- Recorre los artículos pertenecientes a la tienda actual --}}
                @foreach($items as $item)

                    @php
                        // Calcula el importe de la línea y acumula subtotales
                        $importe = $item->precio * $item->cantidad;
                        $subtotalTienda += $importe;
                        $total += $importe;
                    @endphp

                    <article class="carrito-item">

                        {{-- Imagen e información básica del producto --}}
                        <div class="carrito-producto">
                            @if($item->producto?->imagen && file_exists(public_path('storage/' . $item->producto->imagen)))
                                <img
                                    src="{{ asset('storage/' . $item->producto->imagen) }}"
                                    alt=""
                                    class="carrito-imagen">
                            @else
                                <div class="carrito-sin-imagen">
                                    Sin imagen
                                </div>
                            @endif

                            <div class="carrito-producto-info">
                                <h3>
                                    {{ $item->producto?->denominacion ?? 'Producto no disponible' }}
                                </h3>

                                <p>
                                    <strong>Tienda:</strong>
                                    {{ $item->tienda?->nombre ?? 'Tienda no disponible' }}
                                </p>

                                <p>
                                    Precio:
                                    {{ number_format($item->precio, 2) }} €
                                </p>
                            </div>
                        </div>

                        {{-- Selector numérico de cantidad (dispara petición PUT por AJAX al cambiar) --}}
                        <div class="carrito-cantidad">
                            <label>
                                Cantidad
                            </label>

                            <input
                                type="number"
                                class="cantidad-carrito"
                                value="{{ $item->cantidad }}"
                                min="1"
                                data-item="{{ $item->id }}">
                        </div>

                        {{-- Importe subtotal de la línea --}}
                        <div class="carrito-importe">
                            <span>
                                Subtotal
                            </span>

                            <strong id="importe-{{ $item->id }}">
                                {{ number_format($importe, 2) }} €
                            </strong>
                        </div>

                        {{-- Botón para eliminar el artículo del carrito --}}
                        <div class="carrito-accion">
                            <button
                                type="button"
                                class="boton-eliminar"
                                onclick="eliminarItem({{ $item->id }})">
                                Eliminar
                            </button>
                        </div>

                    </article>

                @endforeach

                {{-- Subtotal acumulado correspondiente a la tienda actual --}}
                <div style="text-align:right;margin-bottom:20px;">
                    <strong>
                        Subtotal tienda:
                        {{ number_format($subtotalTienda, 2) }} €
                    </strong>
                </div>

            @endforeach

            {{-- Importe total global de todo el carrito --}}
            <div class="carrito-total">
                <span>
                    Total
                </span>

                <strong id="carrito-total">
                    {{ number_format($total, 2) }} €
                </strong>
            </div>

            {{-- Formulario para procesar el checkout y confirmar la reserva --}}
            <div class="carrito-finalizar">
                <form action="{{ route('carrito.finalizar') }}" method="POST">
                    @csrf

                    <button type="submit" class="boton-guardar">
                        Finalizar reserva
                    </button>
                </form>

                <p>
                    Al finalizar la compra se comprobará
                    el stock disponible y se descontarán
                    las unidades correspondientes.
                </p>
            </div>

        </div>

    @endif
    {{-- Lógica JavaScript para actualizar cantidades y eliminar artículos del carrito mediante AJAX --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.cantidad-carrito').forEach(function (input) {
                input.addEventListener('change', function () {
                    let cantidad = parseInt(this.value);
                    let itemId = this.dataset.item;

                    // Garantiza que la cantidad mínima siempre sea al menos 1
                    if (isNaN(cantidad) || cantidad < 1) {
                        cantidad = 1;
                        this.value = 1;
                    }

                    // Envía la petición PUT al servidor con el token CSRF
                    fetch("{{ url('/carrito') }}/" + itemId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            cantidad: cantidad
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('importe-' + itemId).innerText = data.importe + ' €';
                            document.getElementById('carrito-total').innerText = data.total + ' €';
                        } else {
                            alert(data.message || 'No se pudo actualizar la cantidad.');
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });

        // Elimina un artículo del carrito mediante petición DELETE por AJAX
        function eliminarItem(itemId) {
            fetch("{{ url('/carrito') }}/" + itemId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'No se pudo eliminar el artículo.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>

@endsection
