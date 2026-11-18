@if ($paginator->hasPages())

    <div class="paginacion">

        {{-- ANTERIOR --}}
        @if ($paginator->onFirstPage())
            <span class="disabled">« Anterior</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">« Anterior</a>
        @endif

        {{-- PÁGINAS --}}
        @foreach ($elements as $element)

            {{-- Separador "..." --}}
            @if (is_string($element))
                <span class="disabled">{{ $element }}</span>
            @endif

            {{-- Números --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)

                    @if ($page == $paginator->currentPage())
                        <span class="activo">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif

                @endforeach
            @endif

        @endforeach

        {{-- SIGUIENTE --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Siguiente »</a>
        @else
            <span class="disabled">Siguiente »</span>
        @endif

    </div>

@endif