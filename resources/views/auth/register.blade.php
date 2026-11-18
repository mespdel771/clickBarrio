{{-- Vista de registro para la creación de nuevas cuentas de usuario --}}
<x-guest-layout>

    <h1 class="titulo-auth">
        Crear cuenta
    </h1>

    {{-- Formulario de alta de usuario --}}
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Campo de nombre completo --}}
        <div class="campo-auth">
            <x-input-label
                for="name"
                :value="__('Name')"
                class="etiqueta-auth" />

            <x-text-input
                id="name"
                class="input-auth"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name" />

            <x-input-error
                :messages="$errors->get('name')"
                class="error-auth" />
        </div>

        {{-- Campo de correo electrónico --}}
        <div class="campo-auth">
            <x-input-label
                for="email"
                :value="__('Email')"
                class="etiqueta-auth" />

            <x-text-input
                id="email"
                class="input-auth"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username" />

            <x-input-error
                :messages="$errors->get('email')"
                class="error-auth" />
        </div>

        {{-- Campo de contraseña --}}
        <div class="campo-auth">
            <x-input-label
                for="password"
                :value="__('Password')"
                class="etiqueta-auth" />

            <x-text-input
                id="password"
                class="input-auth"
                type="password"
                name="password"
                required
                autocomplete="new-password" />

            <x-input-error
                :messages="$errors->get('password')"
                class="error-auth" />
        </div>

        {{-- Campo de confirmación de contraseña --}}
        <div class="campo-auth">
            <x-input-label
                for="password_confirmation"
                :value="__('Confirm Password')"
                class="etiqueta-auth" />

            <x-text-input
                id="password_confirmation"
                class="input-auth"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password" />

            <x-input-error
                :messages="$errors->get('password_confirmation')"
                class="error-auth" />
        </div>

        {{-- Enlace al inicio de sesión y botón de envío --}}
        <div class="acciones-auth">
            <a
                class="enlace-auth"
                href="{{ route('login') }}">
                Ya tengo cuenta
            </a>

            <button
                type="submit"
                class="boton-auth">
                Registrarse
            </button>
        </div>

    </form>

</x-guest-layout>
