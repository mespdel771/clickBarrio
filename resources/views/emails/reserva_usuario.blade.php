<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de reserva</title>
</head>
<body>

    <h2>Gracias por tu reserva</h2>

    <p>Hola {{ $reserva->user->name }},</p>

    <p>Tu reserva ha sido realizada correctamente.</p>

    <p><strong>Reserva Nº:</strong> {{ $reserva->id }}</p>
    <p><strong>Fecha:</strong> {{ $reserva->created_at->format('d/m/Y H:i') }}</p>

    @php
        // Agrupa y ordena los productos por el nombre de la tienda de cada item
        $itemsPorTienda = $reserva->items
            ->groupBy(function ($item) {
                return $item->tienda->nombre
                    ?? $item->tienda->denominacion
                    ?? 'Tienda general';
            })
            ->sortKeys();
    @endphp

    @foreach($itemsPorTienda as $nombreTienda => $items)
        <h3>Tienda: {{ $nombreTienda }}</h3>
        <ul>
            @foreach($items as $item)
                <li>
                    {{ $item->producto->denominacion }} ({{ $item->cantidad }}) - {{ number_format($item->importe, 2) }} €
                </li>
            @endforeach
        </ul>
    @endforeach

    <p><strong>Total: {{ number_format($reserva->total, 2) }} €</strong></p>

</body>
</html>
