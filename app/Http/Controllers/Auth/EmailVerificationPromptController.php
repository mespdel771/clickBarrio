<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador encargado de mostrar la pantalla de verificación de correo electrónico.
 */
class EmailVerificationPromptController extends Controller
{
    /**
     * Muestra el aviso de verificación de correo electrónico si el usuario aún no lo ha verificado.
     *
     * @param Request $request Solicitud HTTP actual con el usuario autenticado.
     * @return RedirectResponse|View
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
