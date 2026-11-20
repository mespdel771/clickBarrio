{{-- Vista de bienvenida principal (página de inicio pública) de Las Tiendas del Barrio --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Las Tiendas del Barrio</title>

    {{-- Carga de la hoja de estilos de la pantalla principal mediante Vite --}}
    @vite(['resources/css/principal.css'])
</head>

<body>

    {{-- Tarjeta central de presentación de la plataforma --}}
    <div class="welcome-card">

        {{-- Logotipo vectorial SVG representativo de los comercios locales --}}
        <div class="logo-container">
         
            <img  src="{{ asset('imagen/logo.png') }}"  alt="Logotipo Las Tiendas del Barrio" 
            style="width: 180px !important; height: 80px !important; display: block; margin: 0 auto; object-fit: contain;">
            
        </div>

        {{-- Título principal y subtítulo descriptivo --}}
        <h1>
            Las Tiendas del Barrio a un solo Click
        </h1>

        <p>
            Explora nuestros productos exclusivos.
        </p>

        {{-- Botones de acceso al inicio de sesión y al registro de nuevos usuarios --}}
        <div class="button-group">
            <a href="{{ url('/login') }}" class="btn btn-login">
                Iniciar Sesión
            </a>

            <a href="{{ url('/register') }}" class="btn btn-register">
                Registrarse
            </a>
        </div>

    </div>

</body>

</html>
