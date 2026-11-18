{{-- Vista principal del listado de tiendas agrupadas por categoría con acciones según rol --}}
@extends('layouts.formulario')

@section('contenido')

    {{-- Barra de navegación superior: regreso al panel y alta de nueva tienda (solo administrador) --}}
    <div class="acciones-tabla">
        <a href="{{ route('dashboard') }}" class="boton-volver">
            ← Volver al panel
        </a>

        @if(auth()->user()->hasRole('administrador'))
            <a href="{{ route('tiendas.create') }}" class="boton-guardar">
                + Nueva tienda
            </a>
        @endif
    </div>

    <h2>
        Tiendas
    </h2>

    @php
        // Agrupa la colección de tiendas según la denominación de su categoría
        $tiendasAgrupadas = $tiendas->groupBy(function ($tienda) {
            return $tienda->categoria
                ? $tienda->categoria->denominacion
                : 'Sin categoría';
        });
    @endphp

    {{-- Comprueba si existen tiendas registradas para mostrarlas por bloques de categoría --}}
    @if($tiendas->count() > 0)

        @foreach($tiendasAgrupadas as $categoria => $grupo)

            <h3 class="titulo-categoria">
                {{ $categoria }}
            </h3>

            {{-- Tabla CRUD con las tiendas pertenecientes a la categoría actual --}}
            <div class="tabla-contenedor">
                <table class="tabla-crud">
                    <thead>
                        <tr>
                            <th>Tienda</th>
                            <th>Dirección</th>
                            <th>Acción</th>

                            {{-- Columna adicional visible únicamente para administradores y gestores --}}
                            @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('gestor'))
                                <th>Gestión</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($grupo as $tienda)
                            {{-- Fila clicable que redirige al detalle público de la tienda --}}
                            <tr onclick="window.location='{{ route('tiendas.show', $tienda) }}'"
                                style="cursor:pointer;">

                                {{-- Fotografía y nombre comercial de la tienda --}}
                                <td>
                                    <div style="
                                        display:flex;
                                        flex-direction:column;
                                        align-items:center;
                                        justify-content:center;
                                        text-align:center;
                                        gap:10px;
                                    ">
                                        @if($tienda->imagen)
                                            <img src="{{ asset('storage/'.$tienda->imagen) }}"
                                                 alt="{{ $tienda->nombre }}"
                                                 style="
                                                    width:100px;
                                                    height:100px;
                                                    object-fit:cover;
                                                    border-radius:12px;
                                                 ">
                                        @else
                                            <div style="
                                                width:100px;
                                                height:100px;
                                                border-radius:12px;
                                                background:#eeeeee;
                                                display:flex;
                                                align-items:center;
                                                justify-content:center;
                                                font-size:12px;
                                            ">
                                                Sin imagen
                                            </div>
                                        @endif

                                        <strong>
                                            {{ $tienda->nombre }}
                                        </strong>
                                    </div>
                                </td>

                                {{-- Dirección física de la tienda --}}
                                <td>
                                    {{ $tienda->direccion ?? 'Sin dirección' }}
                                </td>

                                {{-- Enlace directo al escaparate de la tienda (detiene la propagación del clic de fila) --}}
                                <td>
                                    <a href="{{ route('tiendas.show', $tienda) }}"
                                       class="boton-editar"
                                       onclick="event.stopPropagation();">
                                        Ver tienda
                                    </a>
                                </td>

                                {{-- Botones de edición y eliminación según permisos del usuario --}}
                                @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('gestor'))
                                    <td>
                                        {{-- Editar tienda --}}
                                        <a href="{{ route('tiendas.edit', $tienda) }}"
                                           class="boton-editar"
                                           onclick="event.stopPropagation();">
                                            Editar
                                        </a>

                                        {{-- Eliminar tienda (disponible exclusivamente para administradores) --}}
                                        @if(auth()->user()->hasRole('administrador'))
                                            <form action="{{ route('tiendas.destroy', $tienda) }}"
                                                  method="POST"
                                                  style="display:inline; margin-left:10px;"
                                                  onclick="event.stopPropagation();">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="boton-eliminar"
                                                        onclick="event.stopPropagation(); return confirm('¿Estás seguro de que quieres eliminar esta tienda?');">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                @endif

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endforeach

    @else
        <p>
            No hay tiendas disponibles.
        </p>
    @endif

    {{-- Controles de paginación si la colección proviene de un paginador --}}
    @if($tiendas instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="paginacion">
            {{ $tiendas->links('pagination::default') }}
        </div>
    @endif

@endsection
