{{-- Vista principal del listado de categorías disponibles con opciones de gestión para administradores --}}
@extends('layouts.formulario')

@section('contenido')

    {{-- Barra de navegación superior: botón para volver y acceso al alta de categoría (solo administrador) --}}
    <div class="acciones-tabla">
        <a href="{{ url()->previous() }}" class="boton-volver">
            ← Volver
        </a>

        @if(auth()->user()->hasRole('administrador'))
            <a href="{{ route('categorias.create') }}" class="boton-guardar">
                + Nueva categoría
            </a>
        @endif
    </div>

    <h2>
        Categorías disponibles
    </h2>

    {{-- Contenedor principal de la tabla CRUD de categorías --}}
    <div class="tabla-contenedor">
        <table class="tabla-crud">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Tiendas</th>
                    <th>Acción</th>

                    {{-- Columna adicional de edición/borrado visible únicamente para administradores --}}
                    @if(auth()->user()->hasRole('administrador'))
                        <th>Gestión</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                {{-- Recorre las categorías paginadas o muestra un mensaje si la lista está vacía --}}
                @forelse($categorias as $categoria)
                    <tr>
                        {{-- Nombre de la categoría --}}
                        <td>
                            {{ $categoria->denominacion }}
                        </td>

                        {{-- Número total de tiendas asociadas a esta categoría --}}
                        <td>
                            {{ $categoria->tiendas()->count() }}
                        </td>

                        {{-- Enlace para filtrar el listado de tiendas por esta categoría --}}
                        <td>
                            <a href="{{ route('tiendas.index', ['categoria' => $categoria->id]) }}"
                               class="boton-guardar">
                                Ver tiendas
                            </a>
                        </td>

                        {{-- Botones de editar y eliminar disponibles solo para el rol administrador --}}
                        @if(auth()->user()->hasRole('administrador'))
                            <td>
                                <div class="acciones-tabla">
                                    <a href="{{ route('categorias.edit', $categoria) }}"
                                       class="boton-editar">
                                        Editar
                                    </a>

                                    {{-- Formulario de eliminación protegido con confirmación mediante modal --}}
                                    <form method="POST" action="{{ route('categorias.destroy', $categoria) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="button"
                                                class="boton-eliminar"
                                                onclick="abrirModalEliminar(this)">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            No hay categorías disponibles.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Controles de paginación personalizada --}}
    <div class="paginacion">
        {{ $categorias->onEachSide(1)->links('vendor.pagination.simple') }}
    </div>

@endsection
