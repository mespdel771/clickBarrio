{{-- Vista principal del catálogo y gestión de productos adaptada según el rol (Gestor, Administrador o Usuario) --}}
@extends('layouts.formulario')

@section('contenido')

    {{-- Barra de navegación superior: regreso al panel y botón de alta de producto (solo administrador) --}}
    <div class="acciones-tabla">
        <a href="{{ route('dashboard') }}" class="boton-volver">
            ← Volver al panel
        </a>

        @if(auth()->user()->hasRole('administrador'))
            <a href="{{ route('productos.create') }}" class="boton-guardar">
                + Nuevo producto
            </a>
        @endif
    </div>

    <h2>
        Productos
    </h2>

    {{-- Sección para el gestor (o usuarios con tiendas asignadas) --}}
    @if(auth()->user()->hasRole('gestor') || (isset($tiendas) && $tiendas->count()))

        <h2 class="titulo-categoria">
            Productos
        </h2>

        {{-- Listado de tiendas gestionadas por el usuario actual --}}
        @if(isset($tiendas) && $tiendas->count())
            <div class="tiendas-gestor">
                <strong>
                    Tiendas:
                </strong>

                @foreach($tiendas as $tienda)
                    <span>
                        {{ $tienda->nombre }}
                    </span>

                    @if(!$loop->last)
                        ,
                    @endif
                @endforeach
            </div>
        @endif

               {{-- Tabla de productos actualmente activos en las tiendas del gestor --}}
        <div class="tabla-contenedor">
            <table class="tabla-crud">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Imagen</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Gestión</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($productos as $producto)
                        <tr>
                            <td>
                                {{ $producto->denominacion }}
                            </td>

                            <td>
                                @if($producto->imagen && file_exists(public_path('storage/' . $producto->imagen)))
                                    <img src="{{ asset('storage/' . $producto->imagen) }}"
                                         alt=""
                                         style="
                                            width:80px;
                                            height:80px;
                                            object-fit:cover;
                                            border-radius:12px;
                                         ">
                                @else
                                    Sin imagen
                                @endif
                            </td>

                            {{-- Desglose de inventario disponible por cada tienda del gestor --}}
                            <td>
                                @forelse($producto->stocks as $stock)
                                    <div>
                                        <strong>{{ $stock->tienda?->nombre }}:</strong>
                                        {{ $stock->stock }} unidades
                                    </div>
                                @empty
                                    Sin stock (0)
                                @endforelse
                            </td>

                            {{-- Desglose de precio establecido por cada tienda del gestor --}}
                            <td>
                                @forelse($producto->precios as $precio)
                                    <div>
                                        <strong>{{ $precio->tienda?->nombre }}:</strong>
                                        {{ number_format($precio->precio, 2) }} €
                                    </div>
                                @empty
                                    Sin precio (0.00 €)
                                @endforelse
                            </td>

                                                      <td class="acciones-tabla">
                                <a href="{{ route('productos.edit', $producto) }}" class="boton-editar">
                                    Editar
                                </a>

                                {{-- Botón para retirar el producto de las tiendas del gestor --}}
                                <form method="POST"
                                      action="{{ route('productos.removeFromCatalog', $producto) }}"
                                      class="form-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="boton-eliminar"
                                            onclick="abrirModalEliminar(this)">
                                        Eliminar
                                    </button>
                                </form>
                            </td>


                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                No tienes productos en tus tiendas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación de los productos del gestor --}}
        @if($productos instanceof \Illuminate\Pagination\LengthAwarePaginator && $productos->hasPages())
            <div class="paginacion">
                {{ $productos->links('vendor.pagination.custom') }}
            </div>
        @endif

        <hr>

        {{-- Catálogo general de productos aún no añadidos a todas las tiendas del gestor --}}
        <h2 class="titulo-categoria">
            Productos disponibles para añadir
        </h2>

        <div class="tabla-contenedor">
            <table class="tabla-crud">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Imagen</th>
                        <th>Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($productosDisponibles as $producto)
                        <tr>
                            <td>
                                {{ $producto->denominacion }}
                            </td>

                            <td>
                                @if($producto->imagen && \Illuminate\Support\Facades\Storage::disk('public')->exists($producto->imagen))
                                    <img src="{{ asset('storage/' . $producto->imagen) }}"
                                    alt="{{ $producto->denominacion }}"
                                    style="width:80px; height:80px; object-fit:cover; border-radius:8px;">
                                @else
                                    <span>
                                     Sin imagen
                                    </span>
                                 @endif

                            </td>

                            <td>
                                <a href="{{ route('productos.edit', $producto) }}" class="boton-guardar">
                                    Añadir a mi catálogo
                                </a>
                            </td>
                        </tr>
                                    @empty
                        <tr>
                            <td colspan="3">
                                No hay productos disponibles
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    {{-- Sección par el rol administrador --}}
    @elseif(auth()->user()->hasRole('administrador'))

        {{-- Cuadrícula completa de productos con opciones de edición y borrado --}}
        <div class="catalogo-productos">
            @foreach($productos as $producto)
                <article class="producto-catalogo">
                    @if($producto->imagen && \Illuminate\Support\Facades\Storage::disk('public')->exists($producto->imagen))
                        <img src="{{ asset('storage/'.$producto->imagen) }}"
                             alt="{{ $producto->denominacion }}"
                             style="
                                width:120px;
                                height:120px;
                                object-fit:cover;
                                border-radius:12px;
                             ">
                    @else
                        <div style="
                            width:120px;
                            height:120px;
                            border-radius:12px;
                            background:#f3f4f6;
                            color:#6b7280;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:13px;
                            margin: 0 auto;
                        ">
                            Sin imagen
                        </div>
                    @endif

                    <h3>
                        {{ $producto->denominacion }}
                    </h3>

                    <div style="margin-top:10px;">
                        <a href="{{ route('productos.edit', $producto) }}" class="boton-editar">
                            Editar
                        </a>

                        {{-- Formulario de eliminación directa de producto --}}
                        <form action="{{ route('productos.destroy', $producto) }}"
                              method="POST"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="boton-eliminar"
                                    onclick="return confirm('¿Eliminar producto?')">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Paginación del catálogo para administrador --}}
        @if($productos instanceof \Illuminate\Pagination\LengthAwarePaginator && $productos->hasPages())
            <div class="paginacion">
                {{ $productos->links('vendor.pagination.custom') }}
            </div>
        @endif

    {{-- Sección para el rol usuario --}}
    @else

        @php
            // Agrupa los productos paginados por el nombre de su categoría
            $productosAgrupados = $productos
                ->getCollection()
                ->groupBy(fn($p) => $p->categoria->denominacion ?? 'Sin categoría');
        @endphp

        {{-- Recorre cada categoría y muestra los productos junto con sus tiendas, stock y precios --}}
        @foreach($productosAgrupados as $categoria => $grupo)
            <section class="catalogo-categoria">
                <h3 class="titulo-categoria">
                    {{ $categoria }}
                </h3>

                @foreach($grupo as $producto)
                    <article class="producto-catalogo" style="display:flex; align-items:flex-start; gap:1.25rem; padding:1.25rem; border:1px solid #e5e7eb; border-radius:12px; background:#ffffff; margin-bottom:1rem; flex-wrap:wrap;">
                        {{-- Imagen del producto o marcador 'Sin imagen' si no existe el archivo físico --}}
                        @if($producto->imagen && \Illuminate\Support\Facades\Storage::disk('public')->exists($producto->imagen))
                            <img src="{{ asset('storage/' . $producto->imagen) }}"
                                 alt="{{ $producto->denominacion }}"
                                 style="
                                    width:120px;
                                    height:120px;
                                    object-fit:cover;
                                    border-radius:12px;
                                    flex-shrink:0;
                                 ">
                        @else
                            <div style="
                                width:120px;
                                height:120px;
                                border-radius:12px;
                                background:#f3f4f6;
                                color:#6b7280;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-size:13px;
                                flex-shrink:0;
                            ">
                                Sin imagen
                            </div>
                        @endif

                        {{-- Nombre del producto y desglose de establecimientos activos con stock --}}
                        <div style="flex:1; min-width:240px;">
                            <h3 style="margin:0 0 0.75rem 0; color:#1f2937; font-size:1.2rem;">
                                {{ $producto->denominacion }}
                            </h3>

                            <div class="tiendas-producto">
                                @foreach($producto->stocks as $stock)
                                    @continue(!$stock->tienda)
                                    <div class="tienda-producto" style="margin-bottom:0.75rem; padding:0.75rem; background:#f9fafb; border-radius:8px;">
                                        <div class="tienda-nombre" style="color:#111827; font-weight:600;">
                                            🏪 {{ $stock->tienda->nombre }}
                                        </div>

                                        <div class="tienda-info" style="color:#374151; margin:0.35rem 0;">
                                            Stock:
                                            <strong>
                                                {{ $stock->stock }} unidades
                                            </strong>

                                            &nbsp; | &nbsp;

                                            Precio:
                                            <strong>
                                                {{ number_format(
                                                    optional(
                                                        $producto->precios
                                                        ->where('tienda_id', $stock->tienda_id)
                                                        ->first()
                                                    )->precio ?? 0,
                                                    2
                                                ) }} €
                                            </strong>
                                        </div>

                                        <a href="{{ route('tiendas.show', $stock->tienda_id) }}"
                                           class="boton-tienda">
                                            Ver tienda
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @endforeach

        {{-- Paginación del catálogo público para clientes --}}
        @if($productos instanceof \Illuminate\Pagination\LengthAwarePaginator && $productos->hasPages())
            <div class="paginacion">
                {{ $productos->links('vendor.pagination.custom') }}
            </div>
        @endif

    @endif

@endsection
