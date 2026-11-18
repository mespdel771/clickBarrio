<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador encargado de gestionar las categorías en las que se organizan las tiendas y productos.
 *
 * Las operaciones de creación, edición y eliminación están restringidas exclusivamente al rol de administrador.
 */
class CategoriaController extends Controller
{
    /**
     * Muestra el listado paginado de categorías filtrado según el rol del usuario autenticado:
     * - Administrador: todas las categorías.
     * - Gestor: únicamente las categorías asociadas a sus tiendas.
     * - Usuario: todas las categorías (solo lectura).
     *
     * @return View
     */
    public function index(): View
    {
        $user = auth()->user();

        $categorias = Categoria::query();

        if ($user->hasRole('gestor')) {
            $categorias->whereHas('tiendas', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $categorias = $categorias
            ->orderBy('denominacion')
            ->paginate(10);

        return view('categorias.index', compact('categorias'));
    }

    /**
     * Muestra el formulario para crear una nueva categoría (acceso exclusivo para administradores).
     *
     * @return View
     */
    public function create(): View
    {
        if (!auth()->user()->hasRole('administrador')) {
            abort(403);
        }

        return view('categorias.create');
    }

    /**
     * Almacena una nueva categoría en la base de datos (acceso exclusivo para administradores).
     *
     * @param Request $request Solicitud HTTP con la denominación de la categoría.
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (!auth()->user()->hasRole('administrador')) {
            abort(403);
        }

        $request->validate([
            'denominacion' => 'required|string|unique:categorias,denominacion|max:255',
        ]);

        Categoria::create([
            'denominacion' => $request->denominacion,
        ]);

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría creada correctamente');
    }

    /**
     * Muestra el formulario para editar una categoría existente (acceso exclusivo para administradores).
     *
     * @param Categoria $categoria Instancia de la categoría a editar.
     * @return View
     */
    public function edit(Categoria $categoria): View
    {
        if (!auth()->user()->hasRole('administrador')) {
            abort(403);
        }

        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Actualiza los datos de una categoría existente (acceso exclusivo para administradores).
     *
     * @param Request $request Solicitud HTTP con los nuevos datos de la categoría.
     * @param Categoria $categoria Instancia de la categoría a actualizar.
     * @return RedirectResponse
     */
    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        if (!auth()->user()->hasRole('administrador')) {
            abort(403);
        }

        $request->validate([
            'denominacion' =>
                'required|string|max:255|unique:categorias,denominacion,' . $categoria->id,
        ]);

        $categoria->update([
            'denominacion' => $request->denominacion,
        ]);

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente');
    }

    /**
     * Elimina una categoría siempre que no tenga tiendas asociadas (acceso exclusivo para administradores).
     *
     * @param Categoria $categoria Instancia de la categoría a eliminar.
     * @return RedirectResponse
     */
    public function destroy(Categoria $categoria): RedirectResponse
    {
        if (!auth()->user()->hasRole('administrador')) {
            abort(403);
        }

        if ($categoria->tiendas()->count() > 0) {
            return redirect()
                ->route('categorias.index')
                ->with('error', 'No puedes eliminar esta categoría porque tiene tiendas asociadas.');
        }

        $categoria->delete();

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría eliminada correctamente');
    }
}
