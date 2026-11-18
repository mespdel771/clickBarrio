{{-- Vista principal del listado de precios de un producto por tienda con acciones de gestión según rol --}}
@extends('layouts.formulario')

@section('contenido')

    {{-- Barra de navegación superior: regreso a productos y alta de nuevo precio (administrador o gestor) --}}
    <div class="acciones-tabla">
        <a href="{{ route('productos.index') }}" class="boton-volver">
            ← Volver a productos
        </a>

        @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('gestor'))
            <a href="{{ route('precios.create', $producto) }}" class="boton-guardar">
                + Nuevo precio
            </a>
        @endif
    </div>

    <h2>
        Precios de: {{ $producto->denominacion }}
    </h2>

    {{-- Contenedor de la tabla CRUD de precios --}}
    <div class="tabla-contenedor">
        <table class="tabla-crud">
            <thead>
                <tr>
                    <th>Tienda</th>
                    <th>Precio</th>

                    {{-- Columna de gestión visible únicamente para administradores y gestores --}}
                    @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('gestor'))
                        <th>Gestión</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                {{-- Recorre los precios registrados o muestra un aviso si el producto aún no tiene precios --}}
                @forelse($precios as $precio)
                    <tr>
                        {{-- Nombre de la tienda asociada al precio --}}
                        <td>
                            @if($precio->tienda)
                                {{ $precio->tienda->nombre }}
                            @else
                                Tienda eliminada
                            @endif
                        </td>

                        {{-- Importe del precio formateado a dos decimales --}}
                        <td>
                            {{ number_format($precio->precio, 2) }} €
                        </td>

                        {{-- Botones de editar y eliminar para perfiles con permisos de gestión --}}
                        @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('gestor'))
                            <td>
                                <div class="acciones-tabla">
                                    <a href="{{ route('precios.edit', $precio) }}" class="boton-editar">
                                        Editar
                                    </a>

                                    {{-- Formulario de eliminación confirmado mediante ventana modal --}}
                                    <form method="POST" action="{{ route('precios.destroy', $precio) }}">
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
                        <td colspan="3">
                            No hay precios asignados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
