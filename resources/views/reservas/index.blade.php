{{-- Vista principal del historial de reservas agrupadas por pedido y establecimiento --}}
@extends('layouts.formulario')

@section('contenido')

    {{-- Enlace superior de navegación según el rol del usuario autentificado --}}
    <div class="acciones-tabla">
        @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('gestor'))
            <a href="{{ route('dashboard') }}" class="boton-volver">
                ← Volver al panel
            </a>
        @else
            <a href="{{ route('productos.index') }}" class="boton-volver">
                ← Seguir comprando
            </a>
        @endif
    </div>

    <h2>
        Mis compras
    </h2>

    {{-- Comprueba si el usuario tiene reservas registradas --}}
    @if($reservas->count())

        {{-- Recorre cada reserva realizada --}}
        @foreach($reservas as $reserva)

            <div class="carrito-contenedor" style="margin-bottom:2rem;">

                {{-- Cabecera identificativa de la compra con número, fecha e importe total --}}
                <div class="carrito-total" style="margin-bottom:1.5rem;">
                    <div>
                        <strong>
                            Compra #{{ $reserva->id }}
                        </strong>

                        <br>

                        <small>
                            {{ $reserva->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>

                    <strong>
                        Total: {{ number_format($reserva->total, 2) }} €
                    </strong>
                </div>

                @php
                    // Agrupa las líneas de la reserva según la tienda a la que pertenecen
                    $itemsPorTienda = $reserva->items->groupBy('tienda_id');
                @endphp

                {{-- Recorre cada tienda involucrada dentro de la reserva --}}
                @foreach($itemsPorTienda as $tiendaId => $items)

                    @php
                        $tienda = $items->first()->tienda;
                    @endphp

                    <div style="
                        margin-bottom:1.5rem;
                        padding:1rem;
                        border:1px solid #ddd;
                        border-radius:10px;
                    ">
                        <h3 style="margin-top:0;">
                            Tienda: {{ $tienda?->nombre ?? 'Tienda eliminada' }}
                        </h3>

                        {{-- Listado de productos adquiridos en esta tienda --}}
                                              @foreach($items as $item)

                            <article class="carrito-item">
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
                                            {{ $item->producto?->denominacion ?? 'Producto eliminado' }}
                                        </h3>

                                        <p>
                                            <strong>Precio:</strong>
                                            {{ number_format($item->precio, 2) }} €
                                        </p>

                                        <p>
                                            <strong>Cantidad:</strong>
                                            {{ $item->cantidad }}
                                        </p>
                                    </div>
                                </div>
                                {{-- Importe subtotal de la línea --}}
                                <div class="carrito-importe">
                                    <span>
                                        Subtotal
                                    </span>

                                    <strong>
                                        {{ number_format($item->importe, 2) }} €
                                    </strong>
                                </div>
                            </article>

                        @endforeach

                        @php
                            // Calcula el subtotal correspondiente únicamente a esta tienda
                            $totalTienda = $items->sum('importe');
                        @endphp

                        {{-- Subtotal acumulado de la tienda --}}
                        <div class="carrito-total">
                            <span>
                                Total de {{ $tienda?->nombre ?? 'Tienda eliminada' }}
                            </span>

                            <strong>
                                {{ number_format($totalTienda, 2) }} €
                            </strong>
                        </div>
                    </div>

                @endforeach

                {{-- Pie de la compra con el importe total consolidado (las reservas son histórico no modificable) --}}
                <div class="carrito-finalizar">
                    <div class="carrito-total">
                        <span>
                            Total de la compra
                        </span>

                        <strong>
                            {{ number_format($reserva->total, 2) }} €
                        </strong>
                    </div>
                </div>

            </div>

        @endforeach

    @else

        {{-- Estado vacío cuando el usuario aún no ha realizado ninguna compra --}}
        <div class="carrito-vacio">
            <h3>
                No tienes compras
            </h3>

            <p>
                Todavía no has realizado ninguna compra.
            </p>

            <br>

            <a href="{{ route('productos.index') }}" class="boton-guardar">
                Ver productos
            </a>
        </div>

    @endif

@endsection
