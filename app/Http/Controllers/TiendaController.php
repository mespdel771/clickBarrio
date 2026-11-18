<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Controlador encargado de gestionar la visualización, creación, edición y eliminación de tiendas,
 * aplicando filtros y restricciones de acceso según el rol del usuario autenticado.
 */
class TiendaController extends Controller
{
    /**
     * Muestra el listado paginado de tiendas ordenado por categoría y nombre,
     * filtrado según los parámetros de búsqueda y el rol del usuario autenticado:
     * - Administrador: visualiza todas las tiendas registradas.
     * - Gestor: visualiza únicamente las tiendas bajo su gestión.
     * - Usuario: visualiza únicamente las tiendas que disponen de productos con stock activo.
     *
     * @param Request $request Solicitud HTTP con el filtro opcional de categoría.
     * @return View
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $consulta = Tienda::query();

        // Filtra por categorías.
        if ($request->filled('categoria')) {
            $consulta->where('categoria_id', $request->categoria);
        }

        // Gestor solo ve sus tiendas.
        if ($user->hasRole('gestor')) {
            $consulta->where('user_id', $user->id);
        }

        // Usuario solo ve las tiendas que tienen productos en stock.
        if ($user->hasRole('usuario')) {
            $consulta->whereHas('stocks', function ($q) {
                $q->where('stock', '>', 0);
            });
        }

        // Ordenación de tiendas por categorías y después por nombre.
        $tiendas = $consulta
            ->with([
                'categoria',
                'user',
            ])
            ->join(
                'categorias',
                'tiendas.categoria_id',
                '=',
                'categorias.id'
            )
            ->orderBy('categorias.denominacion')
            ->orderBy('tiendas.nombre')
            ->select('tiendas.*')
            ->paginate(10)
            ->withQueryString();

        // Devuelve la vista de tiendas.
        return view(
            'tiendas.index',
            compact('tiendas')
        );
    }

    /**
     * Muestra el detalle de una tienda junto con el catálogo paginado de sus productos con stock disponible,
     * bloqueando el acceso a usuarios estándar si la tienda carece de existencias.
     *
     * @param Tienda $store Instancia de la tienda cuyos detalles se van a mostrar.
     * @return View
     */
    public function show(Tienda $store): View
    {
        // Autenticación de usuario.
        $user = auth()->user();

        // Comprueba si el usuario es un cliente.
        if ($user->hasRole('usuario')) {
            // Comprueba si la tienda tiene algún producto con stock.
            $tieneStock = $store
                ->stocks()
                ->where('stock', '>', 0)
                ->exists();

            // Si no hay stock disponible, bloquea el acceso.
            if (!$tieneStock) {
                abort(403, 'Tienda sin stock disponible');
            }
        }

        // Carga la categoría y el propietario de la tienda.
        $store->load([
            'categoria',
            'user',
        ]);

        // Obtiene los productos disponibles de la tienda.
        $productos = Producto::whereHas('stocks', function ($q) use ($store) {
            $q->where('tienda_id', $store->id)
              ->where('stock', '>', 0);
        })
        ->with([
            'categoria',
            'precios' => fn ($q) => $q->where('tienda_id', $store->id),
            'stocks'  => fn ($q) => $q->where('tienda_id', $store->id),
        ])
        ->orderBy('denominacion')
        ->paginate(10);

        // Envía la tienda y los productos a la vista.
        return view(
            'tiendas.show',
            compact('store', 'productos')
        );
    }

    /**
     * Muestra el formulario para crear una nueva tienda (acceso exclusivo para administradores),
     * cargando el listado de usuarios con rol de gestor disponibles para asignar.
     *
     * @return View
     */
    public function create(): View
    {
        // Autenticación de usuario con rol administrador.
        if (!auth()->user()->hasRole('administrador')) {
            abort(403);
        }

        // Obtiene los usuarios que tienen el rol de gestor.
        $gestores = User::role('gestor')
            ->orderBy('name')
            ->get();

        // Muestra el formulario de creación.
        return view(
            'tiendas.create',
            compact('gestores')
        );
    }

    /**
     * Valida y almacena una nueva tienda en la base de datos junto con su imagen opcional
     * (acceso exclusivo para administradores).
     *
     * @param Request $request Solicitud HTTP con los datos de la tienda e imagen opcional.
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Verificación de rol administrador.
        if (!auth()->user()->hasRole('administrador')) {
            abort(403);
        }

        // Valida los datos recibidos del formulario.
        $datos = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:255',
            'direccion'    => 'nullable|string|max:255',
            'user_id'      => 'nullable|exists:users,id',
            'imagen'       => 'nullable|image|max:2048',
        ]);

        // Comprueba si se ha seleccionado un gestor.
        if (!empty($datos['user_id'])) {
            $usuario = User::findOrFail($datos['user_id']);

            if (!$usuario->hasRole('gestor')) {
                abort(403, 'El usuario no es gestor');
            }
        }

        // Comprueba si se ha enviado una imagen.
        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request
                ->file('imagen')
                ->store('tiendas', 'public');
        }

        // Crea la tienda con los datos validados.
        Tienda::create($datos);

        // Redirige al listado de tiendas.
        return redirect()
            ->route('tiendas.index')
            ->with('success', 'Tienda creada correctamente');
    }

    /**
     * Muestra el formulario de edición de una tienda verificando previamente los permisos
     * según el rol del usuario autenticado (administradores o el gestor propietario).
     *
     * @param Tienda $store Instancia de la tienda a editar.
     * @return View
     */
    public function edit(Tienda $store): View
    {
        // Obtiene el usuario autenticado.
        $user = auth()->user();

        // Solo administradores y gestores pueden editar.
        if (
            !$user->hasRole('administrador')
            && !$user->hasRole('gestor')
        ) {
            abort(403);
        }

        // El usuario gestor solo puede editar su tienda o tiendas.
        if (
            $user->hasRole('gestor')
            && $store->user_id != $user->id
        ) {
            abort(403);
        }

        // Los administradores pueden seleccionar un gestor.
        $gestores = $user->hasRole('administrador')
            ? User::role('gestor')
                ->orderBy('name')
                ->get()
            : null;

        // Muestra el formulario de edición.
        return view(
            'tiendas.edit',
            compact('store', 'gestores')
        );
    }

    /**
     * Valida y actualiza los datos de una tienda existente y su imagen en almacenamiento,
     * restringiendo la reasignación de propietario exclusivamente a administradores.
     *
     * @param Request $request Solicitud HTTP con los nuevos datos de la tienda e imagen opcional.
     * @param Tienda $store Instancia de la tienda a actualizar.
     * @return RedirectResponse
     */
    public function update(Request $request, Tienda $store): RedirectResponse
    {
        // Autenticación de usuario.
        $user = auth()->user();

        // Solo los usuarios administradores o gestores pueden actualizar.
        if (
            !$user->hasRole('administrador')
            && !$user->hasRole('gestor')
        ) {
            abort(403);
        }

        // El gestor solo puede modificar su propia tienda o tiendas.
        if (
            $user->hasRole('gestor')
            && $store->user_id != $user->id
        ) {
            abort(403);
        }

        // Validación de datos recibidos.
        $datos = $request->validate([
            'nombre'    => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'imagen'    => 'nullable|image|max:2048',
            'user_id'   => 'nullable|exists:users,id',
        ]);

        // Comprueba si se ha enviado una nueva imagen.
        if ($request->hasFile('imagen')) {
            // Comprueba si existe la imagen anterior y la elimina.
            if (
                $store->imagen
                && Storage::disk('public')->exists($store->imagen)
            ) {
                Storage::disk('public')->delete($store->imagen);
            }

            // Guarda la nueva imagen.
            $datos['imagen'] = $request
                ->file('imagen')
                ->store('tiendas', 'public');
        }

        // Gestor no puede cambiar propietario.
        if ($user->hasRole('gestor')) {
            // Elimina user_id de los datos a actualizar.
            unset($datos['user_id']);
        }

        // Valida el nuevo propietario si lo cambia un usuario administrador.
        if (
            $user->hasRole('administrador')
            && !empty($datos['user_id'])
        ) {
            $usuario = User::findOrFail($datos['user_id']);

            if (!$usuario->hasRole('gestor')) {
                abort(403, 'El usuario no es gestor');
            }
        }

        // Actualiza la tienda.
        $store->update($datos);

        // Redirige al listado de tiendas.
        return redirect()
            ->route('tiendas.index')
            ->with('success', 'Tienda actualizada correctamente');
    }

    /**
     * Elimina una tienda de la base de datos junto con su imagen almacenada en disco
     * (acceso exclusivo para administradores).
     *
     * @param Tienda $store Instancia de la tienda a eliminar.
     * @return RedirectResponse
     */
      /**
     * Elimina una tienda si no tiene reservas históricas asociadas.
     */
        /**
     * Realiza un borrado lógico (SoftDelete) de la tienda para ocultarla de la aplicación
     * manteniendo intacto el histórico de reservas y las claves foráneas RESTRICT.
     */
    public function destroy(Tienda $store): RedirectResponse
    {
        // Verifica que únicamente un administrador pueda eliminar tiendas
        if (!auth()->user()->hasRole('administrador')) {
            abort(403);
        }

        // Marca la tienda con fecha en 'deleted_at' sin borrarla físicamente de MySQL ni romper reservas
        $store->delete();

        // Redirige al listado de tiendas con mensaje de confirmación
        return redirect()
            ->route('tiendas.index')
            ->with('success', 'Tienda eliminada correctamente.');
    }

}


