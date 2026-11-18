{{-- Vista principal del Panel de Ingresos con métricas globales, por tienda, por producto y por cliente --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Panel de Ingresos</title>

    {{-- Carga de la hoja de estilos específica del panel de ingresos mediante Vite --}}
    @vite(['resources/css/ingresos.css'])
</head>

<body>

    {{-- Enlace de regreso al panel principal (dashboard) --}}
    <a href="{{ route('dashboard') }}" class="back-button">
        ← Volver al panel
    </a>

    <h1>📊 Panel de Ingresos</h1>

    {{-- Indicador clave (KPI) con el importe total acumulado de ingresos --}}
    <div class="kpi">
        💰 Total: {{ number_format($totalIngresos, 2) }} €
    </div>

    {{-- Cuadrícula de tarjetas con el desglose estadístico --}}
    <div class="grid">

        {{-- Tarjeta: desglose de ingresos acumulados por cada tienda --}}
        <div class="card">
            <h2>🏪 Ingresos por tienda</h2>

            @foreach($ingresosPorTienda as $tienda)
                <div class="item">
                    <span class="nombre">{{ $tienda->nombre }}</span>
                    <span class="valor">{{ number_format($tienda->total, 2) }} €</span>
                </div>
            @endforeach
        </div>

        {{-- Tarjeta: desglose de ingresos generados por cada producto --}}
        <div class="card">
            <h2>📦 Ingresos por producto</h2>

            @foreach($ingresosPorProducto as $producto)
                <div class="item">
                    <span class="nombre">{{ $producto->denominacion }}</span>
                    <span class="valor">{{ number_format($producto->total, 2) }} €</span>
                </div>
            @endforeach
        </div>

        {{-- Tarjeta: ranking o listado de clientes según su gasto total acumulado --}}
        <div class="card">
            <h2>👤 Clientes</h2>

            @foreach($clientes as $cliente)
                <div class="item">
                    <span class="nombre">{{ $cliente->name }}</span>
                    <span class="valor">
                        {{ number_format($cliente->total_gastado, 2) }} €
                    </span>
                </div>
            @endforeach
        </div>

    </div>

</body>
</html>
