<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Controlador personalizado encargado de gestionar el registro de nuevos usuarios en la aplicación.
 */
class RegisterController extends Controller
{
    /**
     * Muestra la vista del formulario de registro.
     *
     * @return View
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Procesa los datos del formulario de registro, crea el usuario e inicia su sesión.
     *
     * @param Request $request Solicitud HTTP con los datos de registro (nombre, correo y contraseña).
     * @return RedirectResponse
     */
    public function register(Request $request): RedirectResponse
    {
        // Validar los datos de entrada
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Crear el usuario (El modelo User le asignará el rol 'usuario' automáticamente)
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Iniciar sesión automáticamente
        Auth::login($user);

        // Redireccionar al panel principal
        return redirect()->route('dashboard');
    }
}
