{{-- Vista principal del listado y gestión CRUD de usuarios --}}
@extends('layouts.formulario', [
    'titulo' => 'Gestión de Usuarios'
])

@section('contenido')

    {{-- Botón superior para acceder al formulario de alta de nuevo usuario --}}
    <div class="acciones-tabla">
        <a href="{{ route('usuarios.create') }}" class="boton-guardar">
            + Nuevo Usuario
        </a>
    </div>

    {{-- Contenedor principal de la tabla CRUD de usuarios --}}
    <div class="tabla-contenedor">
        <table class="tabla-crud">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Tienda</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                {{-- Recorre los usuarios registrados o muestra un aviso si la tabla está vacía --}}
                @forelse($usuarios as $usuario)
                    <tr>
                        <td>
                            {{ $usuario->name }}
                        </td>

                        <td>
                            {{ $usuario->email }}
                        </td>

                        {{-- Muestra el rol asignado con la primera letra en mayúscula --}}
                        <td>
                            {{ ucfirst($usuario->getRoleNames()->first() ?? 'Sin rol') }}
                        </td>

                        {{-- Muestra todas las tiendas gestionadas por el usuario o 'Sin asignar' --}}
                        <td>
                            {{ $usuario->tiendas->isNotEmpty() ? $usuario->tiendas->pluck('nombre')->implode(', ') : 'Sin asignar' }}
                        </td>

                        {{-- Botones de editar y eliminar usuario --}}
                        <td class="acciones-tabla">
                            <a href="{{ route('usuarios.edit', $usuario) }}" class="boton-editar">
                                Editar
                            </a>

                            <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" class="form-eliminar">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="boton-eliminar" onclick="abrirModalEliminar(this)">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            No hay usuarios registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
