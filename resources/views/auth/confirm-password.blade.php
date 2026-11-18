{{-- Vista de confirmación de contraseña antes de acceder a un área segura --}}
<x-guest-layout>
    {{-- Mensaje informativo solicitando la validación de identidad --}}
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    {{-- Formulario de verificación de contraseña actual --}}
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        {{-- Campo de contraseña --}}
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Botón de envío para confirmar --}}
        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
