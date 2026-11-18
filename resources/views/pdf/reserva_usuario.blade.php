{{-- Plantilla Blade para la generación del documento PDF de factura de una reserva --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    {{-- Inyecta directamente los estilos CSS de factura en línea para DomPDF --}}
    <style>
        {!! file_get_contents(resource_path('css/factura.css')) !!}
    </style>
</head>
<body>

    <h1>Factura</h1>

    {{-- Datos identificativos de la reserva y del cliente --}}
    <p><strong>Reserva Nº:</strong> {{ $reserva->id }}</p>
    <p><strong>Cliente:</strong> {{ $reserva->user->name }}</p>

    @php
        // Agrupa y ordena los productos por el nombre de la tienda de cada item
        $itemsPorTienda = $reserva->items
            ->groupBy(function ($item) {
                return $item->tienda->nombre ?? 'Tienda general';
            })
            ->sortKeys();
    @endphp

    {{-- Tabla con el desglose de productos agrupados por tienda --}}
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($itemsPorTienda as $nombreTienda => $items)
                <tr>
                    <td colspan="4" style="background-color: #f0f0f0; font-weight: bold; text-align: left;">
                        Tienda: {{ $nombreTienda }}
                    </td>
                </tr>

                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->producto->denominacion }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>{{ number_format($item->precio, 2) }} €</td>
                        <td>{{ number_format($item->importe, 2) }} €</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    {{-- Importe total consolidado de la factura --}}
    <div class="total">
        Total: {{ number_format($reserva->total, 2) }} €
    </div>

</body>
</html>
