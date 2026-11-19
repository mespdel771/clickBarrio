{{-- Vista de detalle público y escaparate de una tienda con listado de productos y compra directa AJAX --}}
@extends('layouts.formulario')

@section('contenido')

    @php
        $user = auth()->user();
    @endphp

    {{-- Botón superior para regresar al listado correspondiente según el rol del usuario --}}
    <div class="acciones-tabla">
        @if($user->hasRole('administrador') || $user->hasRole('gestor'))
            <a href="{{ route('tiendas.index') }}" class="boton-volver">
                ← Volver a tiendas
            </a>
        @else
            <a href="{{ route('productos.index') }}" class="boton-volver">
                ← Volver a productos
            </a>
        @endif
    </div>

    {{-- Información general y datos de contacto de la tienda --}}
    <h2>
        {{ $store->nombre }}
    </h2>

    {{-- Fotografía del establecimiento --}}
    @if($store->imagen)
        <div style="margin:20px 0;">
            <img src="{{ asset('storage/'.$store->imagen) }}"
                 alt="{{ $store->nombre }}"
                 style="
                    width:300px;
                    height:200px;
                    object-fit:cover;
                    border-radius:12px;
                 ">
        </div>
    @else
        <p>
            Sin imagen de tienda
        </p>
    @endif

    <p>
        <strong>Dirección:</strong>
        {{ $store->direccion ?? 'Sin dirección' }}
    </p>

    @if($store->telefono)
        <p>
            <strong>Teléfono:</strong>
            {{ $store->telefono }}
        </p>
    @endif

    @if($store->email)
        <p>
            <strong>Email:</strong>
            {{ $store->email }}
        </p>
    @endif

    {{-- Contenedores de alertas dinámicas para respuestas AJAX del carrito --}}
    <div id="mensaje-carrito" style="display:none;" class="mensaje-exito"></div>
    <div id="mensaje-carrito-error" style="display:none;" class="mensaje-error"></div>

    {{-- Sección de catálogo de productos disponibles en esta tienda --}}
    <h2 style="margin-top:2rem;">
        Productos disponibles
    </h2>

    @if($productos->isEmpty())
        <div class="mensaje-error">
            No hay productos disponibles en esta tienda.
        </div>
    @else
        <div class="tabla-contenedor">
            <table class="tabla-crud">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Imagen</th>
                        <th>Precio</th>
                        <th>Stock</th>

                        {{-- Columnas de compra directa visibles únicamente para usuarios con rol cliente --}}
                        @if(auth()->check() && auth()->user()->hasRole('usuario'))
                            <th>Cantidad</th>
                            <th>Acción</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @foreach($productos as $producto)
                        @php
                            $stock = $producto->stocks->first();
                            $precio = $producto->precios->first();
                            $stockDisponible = $stock?->stock ?? 0;
                        @endphp

                        <tr>
                            <td>
                                <strong>
                                    {{ $producto->denominacion }}
                                </strong>
                            </td>

                            <td>
                                {{ $producto->categoria?->denominacion ?? 'Sin categoría' }}
                            </td>

                                                      <td>
                                @if($producto->imagen && \Illuminate\Support\Facades\Storage::disk('public')->exists($producto->imagen))
                                    <img src="{{ asset('storage/'.$producto->imagen) }}"
                                         alt="{{ $producto->denominacion }}"
                                         style="
                                            width:80px;
                                            height:80px;
                                            object-fit:cover;
                                            border-radius:8px;
                                         ">
                                @else
                                    <span>
                                        Sin imagen
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($precio)
                                    {{ number_format($precio->precio, 2) }} €
                                @else
                                    <span>
                                        Sin precio
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($stockDisponible > 0)
                                    {{ $stockDisponible }} unidades
                                @else
                                    <span>
                                        Agotado
                                    </span>
                                @endif
                            </td>
                            {{-- Controles de selección de cantidad y botón de compra para clientes --}}
                            @if(auth()->check() && auth()->user()->hasRole('usuario'))
                                <td>
                                    @if($stockDisponible > 0 && $precio)
                                        <input type="number"
                                               class="cantidad-producto"
                                               id="cantidad-{{ $producto->id }}"
                                               value="1"
                                               min="1"
                                               max="{{ $stockDisponible }}"
                                               style="width:70px;">
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    @if($stockDisponible > 0 && $precio)
                                        <button type="button"
                                                class="boton-guardar boton-anadir-carrito"
                                                id="boton-carrito-{{ $producto->id }}"
                                                onclick="añadirAlCarrito(
                                                    {{ $producto->id }},
                                                    {{ $store->id }},
                                                    {{ $stockDisponible }}
                                                )">
                                            Añadir al carrito
                                        </button>
                                    @else
                                        <span>
                                            No disponible
                                        </span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Lógica JavaScript para añadir productos al carrito mediante petición POST AJAX --}}
    @if(auth()->check() && auth()->user()->hasRole('usuario'))
        <script>
            // Envía al servidor el producto, tienda y unidades seleccionadas
            function añadirAlCarrito(productoId, tiendaId, stock) {
                let cantidad = document.getElementById('cantidad-' + productoId).value;

                fetch("{{ route('carrito.add') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        producto_id: productoId,
                        tienda_id: tiendaId,
                        cantidad: cantidad
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("mensaje-carrito").style.display = "block";
                        document.getElementById("mensaje-carrito").innerHTML = data.message;
                    } else {
                        document.getElementById("mensaje-carrito-error").style.display = "block";
                        document.getElementById("mensaje-carrito-error").innerHTML = data.message;
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                });
            }
        </script>
    @endif

@endsection
