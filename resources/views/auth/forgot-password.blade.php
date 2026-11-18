{{-- Vista de solicitud de enlace para restablecer la contraseña olvidada --}}
<x-guest-layout>
    {{-- Texto explicativo indicando el envío del enlace al correo electrónico --}}
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    {{-- Estado de la sesión (muestra el aviso cuando el correo ha sido enviado) --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Formulario de envío de enlace de recuperación --}}
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Campo de correo electrónico --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Botón para enviar el enlace de restablecimiento --}}
        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
