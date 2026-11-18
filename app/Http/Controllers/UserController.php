<?php

namespace App\Http\Controllers;

use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Controlador encargado de gestionar la administración de usuarios,
 * la asignación de sus roles y su vinculación como gestores de tiendas.
 */
class UserController extends Controller
{
    /**
     * Muestra el listado completo de usuarios junto con sus tiendas asociadas.
     *
     * @return View
     */
    public function index(): View
    {
        // Obtiene los usuarios cargando su relación de tiendas.
        $usuarios = User::with('tiendas')->get();

        // Redirige a la vista de usuarios.
        return view(
            'usuarios.index',
            compact('usuarios')
        );
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     *
     * @return View
     */
    public function create(): View
    {
        // Obtiene todas las tiendas disponibles.
        $tiendas = Tienda::all();

        // Envía las tiendas al formulario.
        return view(
            'usuarios.create',
            compact('tiendas')
        );
    }

    /**
     * Valida y almacena un nuevo usuario en la base de datos, asignándole su rol
     * y vinculándolo a una tienda si su rol es gestor.
     *
     * @param Request $request Solicitud HTTP con nombre, correo, contraseña, rol y tienda opcional.
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Valida los datos.
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:8',
            'rol'       => 'required|in:administrador,gestor,usuario',
            'tienda_id' => 'nullable|exists:tiendas,id',
        ]);

        // Crea el nuevo usuario.
        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = Hash::make($request->password);
        $usuario->save(); // Guardamos primero para generar el ID del usuario.

        // Si es gestor, guardamos el ID del usuario en la columna 'user_id' de la tabla 'tiendas'.
        if ($request->rol === 'gestor' && $request->filled('tienda_id')) {
            $tienda = Tienda::find($request->tienda_id);
            if ($tienda) {
                $tienda->user_id = $usuario->id;
                $tienda->save();
            }
        }

        // Asignamos el rol elegido.
        $usuario->assignRole($request->rol);

        // Redirige al listado de usuarios.
        return redirect()
            ->route('usuarios.index')
            ->with(
                'success',
                'Usuario creado correctamente'
            );
    }

    /**
     * Muestra el formulario para editar los datos, rol y tienda asignada de un usuario existente.
     *
     * @param User $usuario Instancia del usuario a editar.
     * @return View
     */
    public function edit(User $usuario): View
    {
        // Obtiene todas las tiendas disponibles.
        $tiendas = Tienda::all();

        // Vista del formulario de edición de usuarios.
        return view(
            'usuarios.edit',
            compact(
                'usuario',
                'tiendas'
            )
        );
    }

    /**
     * Valida y actualiza los datos de un usuario existente, sincroniza su rol
     * y actualiza su asignación como gestor de tienda si corresponde.
     *
     * @param Request $request Solicitud HTTP con los nuevos datos del usuario.
     * @param User $usuario Instancia del usuario a actualizar.
     * @return RedirectResponse
     */
    public function update(Request $request, User $usuario): RedirectResponse
    {
        // Validación de datos.
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email,' . $usuario->id,
            'rol'       => 'required|in:administrador,gestor,usuario',
            'tienda_id' => 'nullable|exists:tiendas,id',
        ]);

        // Actualización de datos de usuario.
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->save();

        // 1. Limpiamos cualquier vinculación previa que tuviera este usuario con alguna tienda.
        Tienda::where('user_id', $usuario->id)->update(['user_id' => null]);

        // 2. Si sigue siendo gestor y seleccionó una tienda, le asignamos el usuario a la nueva tienda.
        if ($request->rol === 'gestor' && $request->filled('tienda_id')) {
            $tienda = Tienda::find($request->tienda_id);
            if ($tienda) {
                $tienda->user_id = $usuario->id;
                $tienda->save();
            }
        }

        // Actualizar rol de usuario.
        $usuario->syncRoles([]);
        $usuario->assignRole($request->rol);

        // Redirige a la vista de usuarios.
        return redirect()
            ->route('usuarios.index')
            ->with(
                'success',
                'Usuario actualizado correctamente'
            );
    }

    /**
     * Elimina un usuario de la base de datos tras desvincularlo de cualquier tienda que gestionara.
     *
     * @param User $usuario Instancia del usuario a eliminar.
     * @return RedirectResponse
     */
    public function destroy(User $usuario): RedirectResponse
    {
        // Antes de eliminar al usuario, dejamos su tienda libre en la base de datos.
        Tienda::where('user_id', $usuario->id)->update(['user_id' => null]);

        // Eliminación de usuario.
        $usuario->delete();

        // Redirige a la vista de usuarios.
        return redirect()
            ->route('usuarios.index')
            ->with(
                'success',
                'Usuario eliminado correctamente'
            );
    }
}
