{{-- Componente reutilizable para mostrar la lista de errores de validación de un campo de formulario --}}
@props(['messages'])

{{-- Renderiza la lista en rojo solo si existen mensajes de error asociados --}}
@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
