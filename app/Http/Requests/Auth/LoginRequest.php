<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * FormRequest personalizado encargado de validar las credenciales de inicio de sesión
 * y gestionar la autentificación junto con el límite de intentos fallidos (rate limiting).
 */
class LoginRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud de inicio de sesión.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Intenta autentificar las credenciales de la solicitud verificando previamente
     * que no se haya superado el límite de intentos fallidos.
     *
     * @return void
     *
     * @throws ValidationException Si las credenciales proporcionadas no son válidas.
     */
    public function authenticate(): void
    {
        // Comprueba que no se hayan superado los intentos permitidos.
        $this->ensureIsNotRateLimited();

        // Comprueba email y contraseña e inicia sesión si son correctos.
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Registra un intento fallido.
            RateLimiter::hit($this->throttleKey());

            // Devuelve un error indicando que las credenciales no son válidas.
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Si el login es correcto, reinicia el contador de intentos.
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Asegura que la solicitud de inicio de sesión no esté bloqueada por exceso de intentos.
     *
     * @return void
     *
     * @throws ValidationException Si se ha superado el límite máximo de 5 intentos permitidos.
     */
    public function ensureIsNotRateLimited(): void
    {
        // Permite continuar si no ha superado los 5 intentos.
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        // Genera el evento de bloqueo de inicio de sesión.
        event(new Lockout($this));

        // Obtiene los segundos que faltan para poder volver a intentarlo.
        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Muestra un mensaje indicando cuánto debe esperar.
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Obtiene la clave única de limitación de intentos (rate limiting) para la solicitud.
     *
     * @return string
     */
    public function throttleKey(): string
    {
        // Combina el email y la IP para identificar los intentos.
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
