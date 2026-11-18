{{-- Vista de inicio de sesión de la aplicación "Las Tiendas del Barrio" --}}
<x-guest-layout>

    {{-- Cabecera visual del formulario de acceso --}}
    <div class="cabecera-login">
        <div class="icono-login">
            🏪
        </div>

        <h1 class="titulo-auth">
            Las Tiendas del Barrio
        </h1>

        <p class="subtitulo-auth">
            Accede a tu cuenta
        </p>
    </div>

    {{-- Mensaje de estado de la sesión (ej. tras restablecer contraseña) --}}
    <x-auth-session-status
        class="mensaje-auth"
        :status="session('status')" />

    {{-- Formulario de autenticación de usuario --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Campo de correo electrónico --}}
        <div class="campo-auth">
            <label class="etiqueta-auth">
                Correo electrónico
            </label>

            <input
                id="email"
                class="input-auth"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Introduce tu email"
                required
                autofocus>

            <x-input-error
                :messages="$errors->get('email')"
                class="error-auth" />
        </div>

        {{-- Campo de contraseña --}}
        <div class="campo-auth">
            <label class="etiqueta-auth">
                Contraseña
            </label>

            <input
                id="password"
                class="input-auth"
                type="password"
                name="password"
                placeholder="Introduce tu contraseña"
                required>

            <x-input-error
                :messages="$errors->get('password')"
                class="error-auth" />
        </div>

        {{-- Opción para mantener la sesión iniciada --}}
        <div class="recordar-auth">
            <label>
                <input
                    type="checkbox"
                    name="remember"
                    class="checkbox-auth">

                Recordarme
            </label>
        </div>

        {{-- Enlace de recuperación de contraseña y botón de envío --}}
        <div class="acciones-login">
            <a
                href="{{ route('password.request') }}"
                class="enlace-auth">
                ¿Olvidaste la contraseña?
            </a>

            <button
                type="submit"
                class="boton-auth">
                Entrar
            </button>
        </div>

        {{-- Enlace al formulario de registro para nuevos usuarios --}}
        <div class="registro-login">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}">
                Crear cuenta
            </a>
        </div>

    </form>

</x-guest-layout>
