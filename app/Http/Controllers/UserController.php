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
        // Obtiene todas las tiendas disponibles junto con su gestor actual.
        $tiendas = Tienda::with('user')
            ->orderBy('nombre')
            ->get();

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
            'tienda_id' => 'required_if:rol,gestor|nullable|exists:tiendas,id',
        ], [
            'tienda_id.required_if' => 'Debes seleccionar una tienda para asignar al gestor.',
        ]);

        // Crea el nuevo usuario.
        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = Hash::make($request->password);
        $usuario->save(); // Guardamos primero para generar el ID del usuario.

        // Si es gestor, asignamos la tienda y cambiamos al antiguo gestor a 'usuario' si se queda sin tiendas.
        if ($request->rol === 'gestor' && $request->filled('tienda_id')) {
            $this->asignarTiendaAGestor($request->tienda_id, $usuario->id);
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
        // Obtiene todas las tiendas disponibles junto con su gestor actual.
        $tiendas = Tienda::with('user')
            ->orderBy('nombre')
            ->get();

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
            'password'  => 'nullable|min:8',
            'rol'       => 'required|in:administrador,gestor,usuario',
            'tienda_id' => 'required_if:rol,gestor|nullable|exists:tiendas,id',
        ], [
            'tienda_id.required_if' => 'Debes seleccionar una tienda para asignar al gestor.',
        ]);

        // Actualización de datos de usuario.
        $usuario->name = $request->name;
        $usuario->email = $request->email;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        // 1. Limpiamos cualquier vinculación previa que tuviera este usuario con alguna tienda.
        Tienda::where('user_id', $usuario->id)->update(['user_id' => null]);

        // 2. Si es gestor y seleccionó una tienda, la asignamos y cambiamos al antiguo gestor si procede.
        if ($request->rol === 'gestor' && $request->filled('tienda_id')) {
            $this->asignarTiendaAGestor($request->tienda_id, $usuario->id);
        }

        // Actualizar rol de usuario.
        $usuario->syncRoles([$request->rol]);

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

    /**
     * Asigna un usuario como gestor de una tienda y, si la tienda tenía un gestor anterior
     * que ya no gestiona ninguna otra tienda, cambia su rol a 'usuario'.
     *
     * @param int|string $tiendaId ID de la tienda a asignar.
     * @param int $nuevoGestorId ID del nuevo usuario gestor.
     * @return void
     */
    private function asignarTiendaAGestor($tiendaId, int $nuevoGestorId): void
    {
        $tienda = Tienda::find($tiendaId);

        if (!$tienda) {
            return;
        }

        $antiguoGestorId = $tienda->user_id;

        // Asignamos el nuevo gestor a la tienda.
        $tienda->user_id = $nuevoGestorId;
        $tienda->save();

        // Si había un gestor anterior distinto y ya no tiene más tiendas a su cargo, pasa a rol 'usuario'.
        if ($antiguoGestorId && $antiguoGestorId !== $nuevoGestorId) {
            $tieneOtrasTiendas = Tienda::where('user_id', $antiguoGestorId)->exists();

            if (!$tieneOtrasTiendas) {
                $antiguoGestor = User::find($antiguoGestorId);
                $antiguoGestor?->syncRoles(['usuario']);
            }
        }
    }
}
