{{-- Vista de detalle individual de una reserva realizada por el usuario --}}
@extends('layouts.formulario')

@section('contenido')

    {{-- Botón superior para volver al catálogo de productos --}}
    <div class="acciones-tabla">
        <a href="{{ route('productos.index') }}" class="boton-volver">
            ← Seguir reservando
        </a>
    </div>

    {{-- Mensaje de éxito en sesión (ej. al confirmar una nueva compra) --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h2>
        Detalle de la reserva
    </h2>

    {{-- Contenedor principal con la cabecera y el desglose de líneas de la reserva --}}
    <div class="carrito-contenedor">

        <h3>
            Reserva nº {{ $reserva->id }}
        </h3>

        <p>
            Fecha: {{ $reserva->created_at->format('d/m/Y H:i') }}
        </p>

        @php
            // Inicializa el acumulador del importe total de la reserva
            $total = 0;
        @endphp

        {{-- Recorre cada artículo incluido en la reserva --}}
        @foreach($reserva->items as $item)

            @php
                // Calcula el subtotal de la línea y lo suma al total general
                $importe = $item->precio * $item->cantidad;
                $total += $importe;
            @endphp

            <article class="carrito-item">
                {{-- Datos del producto, tienda asociada, cantidad y precio unitario --}}
                <div class="carrito-producto">
                    <div class="carrito-producto-info">
                        <h3>
                            {{ $item->producto->denominacion }}
                        </h3>

                        <p>
                            <strong>Tienda:</strong>
                            {{ $item->tienda->nombre }}
                        </p>

                        <p>
                            Cantidad: {{ $item->cantidad }}
                        </p>

                        <p>
                            Precio: {{ number_format($item->precio, 2) }} €
                        </p>
                    </div>
                </div>

                {{-- Importe subtotal de la línea --}}
                <div class="carrito-importe">
                    <strong>
                        {{ number_format($importe, 2) }} €
                    </strong>
                </div>
            </article>

        @endforeach

        {{-- Importe total consolidado de toda la reserva --}}
        <div class="carrito-total">
            <span>
                Total
            </span>

            <strong>
                {{ number_format($total, 2) }} €
            </strong>
        </div>

    </div>

@endsection
