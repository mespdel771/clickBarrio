{{-- Componente visual con el logotipo vectorial SVG de "Las Tiendas del Barrio" --}}
<div class="logo-container">
    {{-- Estilo inline forzado para evitar conflictos con clases globales de ancho/alto --}}
    <svg xmlns="http://w3.org" fill="none" viewBox="0 0 48 24" stroke-width="1.5" style="width: 180px !important; height: 90px !important; display: block;">

        {{-- Línea base del suelo que une los comercios en color azul índigo (#4f46e5) --}}
        <path stroke-linecap="round" stroke-linejoin="round" stroke="#4f46e5" d="M2 22h44" />

        {{-- Tienda 1 (Izquierda): Alimentación / Verdulería --}}
        <g class="store-grocery" stroke="#4f46e5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 22V11m12 11V11M4 11l1.5-3h9l1.5 3M4 11h12" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 17a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z M8 15V14" />
        </g>

        {{-- Tienda 2 (Centro): Moda / Boutique --}}
        <g class="store-boutique" stroke="#4f46e5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 22V8a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14M18 11h12" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M24 14a1.5 1.5 0 0 1 1.5-1.5M21 17h6l-3-3.5-3 3.5Z" />
        </g>

        {{-- Tienda 3 (Derecha): Cafetería / Panadería --}}
        <g class="store-bakery" stroke="#4f46e5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M32 22V12m12 11V12M32 12l2-3h8l2 3M32 12h12" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M36 18h3.5a1.5 1.5 0 0 0 1.5-1.5V15h-5v3Z" />
        </g>
    </svg>
</div>
