{{-- Componente encargado de mostrar mensajes de estado de la sesión (por ejemplo, confirmaciones de envío de correo) --}}
@props(['status'])

{{-- Renderiza el mensaje solo si existe un valor activo en la variable $status --}}
@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
