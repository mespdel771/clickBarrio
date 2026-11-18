{{-- Componente reutilizable de menú desplegable (dropdown) gestionado con Alpine.js --}}
@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
    // Determina las clases CSS de alineación horizontal o superior del menú
    $alignmentClasses = match ($align) {
        'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
        'top' => 'origin-top',
        default => 'ltr:origin-top-right rtl:origin-top-left end-0',
    };

    // Define el ancho del contenedor desplegable
    $width = match ($width) {
        '48' => 'w-48',
        default => $width,
    };
@endphp

{{-- Contenedor principal con estado reactivo de Alpine.js (se cierra al hacer clic fuera) --}}
<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    {{-- Elemento disparador (trigger) que alterna la visibilidad del menú --}}
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    {{-- Panel desplegable con transiciones suaves de apertura y cierre --}}
    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        {{-- Contenido interno del menú desplegable --}}
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
