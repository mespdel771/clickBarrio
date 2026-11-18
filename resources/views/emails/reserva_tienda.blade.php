{{-- Plantilla de correo electrónico enviada a la tienda al registrarse una nueva reserva --}}
<h2>Nueva reserva</h2>

{{-- Datos identificativos del establecimiento y cabecera de la reserva --}}
<p><strong>Tienda:</strong> {{ $tienda->nombre }}</p>
<p><strong>Reserva Nº:</strong> {{ $reserva->id }}</p>
<p><strong>Fecha:</strong> {{ $reserva->created_at->format('d/m/Y H:i') }}</p>

<hr>

{{-- Datos de contacto del cliente que realizó el pedido --}}
<p><strong>Cliente:</strong> {{ $reserva->user->name }}</p>
<p><strong>Email:</strong> {{ $reserva->user->email }}</p>

<hr>

{{-- Listado detallado de los productos reservados pertenecientes a esta tienda --}}
<ul>
    @foreach($items as $item)
        <li>
            {{ $item->producto->denominacion }}
            ({{ $item->cantidad }}) -
            {{ number_format($item->importe, 2) }} €
        </li>
    @endforeach
</ul>

{{-- Importe total acumulado correspondiente únicamente a esta tienda --}}
<p><strong>Total tienda:</strong> {{ number_format($items->sum('importe'), 2) }} €</p>
