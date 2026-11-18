<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Controlador encargado de gestionar la visualización, actualización y eliminación del perfil del usuario.
 */
class ProfileController extends Controller
{
    /**
     * Muestra el formulario de edición del perfil del usuario autenticado.
     *
     * @param Request $request Solicitud HTTP actual con el usuario autenticado.
     * @return View
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Actualiza los datos del perfil del usuario autenticado.
     *
     * @param ProfileUpdateRequest $request Solicitud validada con los nuevos datos del perfil.
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Asigna al usuario únicamente los datos validados.
        $request->user()->fill($request->validated());

        // Si cambia el email, será necesario verificarlo de nuevo.
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Guarda los cambios en la base de datos.
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Elimina permanentemente la cuenta del usuario autenticado previa confirmación de contraseña.
     *
     * @param Request $request Solicitud HTTP que incluye la contraseña actual de confirmación.
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Comprueba que el usuario confirme su contraseña actual.
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        // Guarda una referencia al usuario antes de eliminarlo.
        $user = $request->user();

        // Cierra la sesión del usuario.
        Auth::logout();

        // Elimina la cuenta de la base de datos.
        $user->delete();

        // Invalida la sesión y genera un nuevo token CSRF.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirige al usuario a la página principal.
        return Redirect::to('/');
    }
}
