{{-- Componente reutilizable para las etiquetas (<label>) de los campos de formulario --}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{-- Muestra el texto pasado por el atributo 'value' o, en su defecto, el contenido del slot --}}
    {{ $value ?? $slot }}
</label>
