<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Controlador encargado de gestionar el establecimiento de una nueva contraseña mediante el token de recuperación.
 */
class NewPasswordController extends Controller
{
    /**
     * Muestra la vista de restablecimiento de contraseña.
     *
     * @param Request $request Solicitud HTTP que contiene el token y el correo electrónico.
     * @return View
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Procesa una solicitud entrante de nueva contraseña.
     *
     * @param Request $request Solicitud HTTP con el token, correo y nueva contraseña.
     * @return RedirectResponse
     *
     * @throws ValidationException Si los datos proporcionados no superan la validación.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        /*
         * Aquí intentaremos restablecer la contraseña del usuario. Si tiene éxito,
         * actualizaremos la contraseña en el modelo de usuario real y la guardaremos
         * en la base de datos. De lo contrario, analizaremos el error y devolveremos la respuesta.
         */
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        /*
         * Si la contraseña se restableció correctamente, redirigiremos al usuario
         * de vuelta a la vista de inicio de sesión. Si hay un error, lo redirigiremos
         * de vuelta al formulario con su mensaje de error.
         */
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
