{{-- Plantilla Blade para el documento PDF / correo de notificación de nueva reserva a una tienda --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    {{-- Inyecta directamente los estilos CSS de factura para que DomPDF los procese en línea --}}
    <style>
        {!! file_get_contents(resource_path('css/factura.css')) !!}
    </style>
</head>

<body>

    <h1>
        Nueva reserva
    </h1>

    {{-- Datos identificativos de la tienda y cabecera de la reserva --}}
    <p>
        <strong>Tienda:</strong>
        {{ $tienda->nombre }}
    </p>

    <p>
        <strong>Reserva Nº:</strong>
        {{ $reserva->id }}
    </p>

    <p>
        <strong>Fecha:</strong>
        {{ $reserva->created_at->format('d/m/Y H:i') }}
    </p>

    <hr>

    {{-- Datos de contacto del cliente que realizó la reserva --}}
    <p>
        <strong>Cliente:</strong>
        {{ $reserva->user->name }}
    </p>

    <p>
        <strong>Email:</strong>
        {{ $reserva->user->email }}
    </p>

    <hr>

    {{-- Tabla con el desglose de productos reservados correspondientes a esta tienda --}}
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
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->producto->denominacion }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>{{ number_format($item->precio, 2) }} €</td>
                    <td>{{ number_format($item->importe, 2) }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    {{-- Importe total acumulado de los artículos pertenecientes a la tienda --}}
    <h2 class="total">
        Total tienda:
        {{ number_format($items->sum('importe'), 2) }} €
    </h2>

    <p>
        Preparar los productos indicados para la reserva del cliente.
    </p>

</body>
</html>
