<?php

namespace App\Http\Controllers;

use App\Models\Precio;
use App\Models\Producto;
use App\Models\Tienda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador encargado de gestionar los precios asignados a cada producto por tienda.
 *
 * Controla el acceso según el rol del usuario: los administradores pueden gestionar
 * todos los precios y los gestores únicamente los de sus tiendas asociadas.
 */
class PrecioController extends Controller
{
    /**
     * Muestra el listado de precios de un producto según el rol del usuario autenticado.
     *
     * @param Producto $producto Instancia del producto cuyos precios se van a consultar.
     * @return View
     */
    public function index(Producto $producto): View
    {
        // Obtiene el usuario autenticado.
        $user = auth()->user();

        // Verifica que el usuario tenga rol válido.
        if (
            !$user->hasRole('administrador')
            && !$user->hasRole('gestor')
        ) {
            abort(403);
        }

        if ($user->hasRole('administrador')) {
            $precios = $producto
                ->precios()
                ->with('tienda')
                ->get();
        } else {
            // Obtiene las tiendas asociadas al gestor.
            $tiendas = Tienda::where('user_id', $user->id)
                ->pluck('id');

            // Comprueba que el producto pertenece a la tienda o tiendas del gestor.
            $tieneAcceso = $producto
                ->precios()
                ->whereIn('tienda_id', $tiendas)
                ->exists();

            if (!$tieneAcceso) {
                abort(403);
            }

            // Obtiene solo los precios de sus tiendas.
            $precios = $producto
                ->precios()
                ->whereIn('tienda_id', $tiendas)
                ->with('tienda')
                ->get();
        }

        // Devuelve la vista con los datos.
        return view(
            'precios.index',
            compact(
                'producto',
                'precios'
            )
        );
    }

    /**
     * Muestra el formulario para asignar un nuevo precio a un producto según el rol del usuario:
     * - Administrador: permite seleccionar cualquier tienda del sistema.
     * - Gestor: permite seleccionar únicamente sus propias tiendas si tiene acceso al producto.
     *
     * @param Producto $producto Instancia del producto al que se le asignará el precio.
     * @return View
     */
    public function create(Producto $producto): View
    {
        // Obtiene el usuario autenticado.
        $user = auth()->user();

        // Verifica permisos de acceso.
        if (
            !$user->hasRole('administrador')
            && !$user->hasRole('gestor')
        ) {
            abort(403);
        }

        // Obtiene las tiendas disponibles según el rol.
        if ($user->hasRole('administrador')) {
            $tiendas = Tienda::orderBy('nombre')->get();
        } else {
            $tiendas = Tienda::where('user_id', $user->id)
                ->orderBy('nombre')
                ->get();

            // Verifica acceso al producto.
            $tieneAcceso = $producto
                ->precios()
                ->whereIn('tienda_id', $tiendas->pluck('id'))
                ->exists();

            if (!$tieneAcceso) {
                abort(403);
            }
        }

        // Devuelve la vista de creación de precio.
        return view(
            'precios.create',
            compact(
                'producto',
                'tiendas'
            )
        );
    }

    /**
     * Guarda o actualiza el precio de un producto en una tienda seleccionada,
     * verificando previamente que el gestor tenga permisos sobre dicha tienda.
     *
     * @param Request $request Solicitud HTTP con tienda_id y precio.
     * @param Producto $producto Instancia del producto al que se asigna el precio.
     * @return RedirectResponse
     */
    public function store(Request $request, Producto $producto): RedirectResponse
    {
        // Valida los datos de entrada.
        $datos = $request->validate([
            'tienda_id' => 'required|exists:tiendas,id',
            'precio'    => 'required|numeric|min:0',
        ]);

        // Busca la tienda seleccionada.
        $tienda = Tienda::findOrFail($datos['tienda_id']);

        $user = auth()->user();

        // El gestor solo puede usar sus tiendas.
        if ($user->hasRole('gestor') && $tienda->user_id != $user->id) {
            abort(403);
        }

        // Crea o actualiza el precio del producto en la tienda.
        Precio::updateOrCreate(
            [
                'producto_id' => $producto->id,
                'tienda_id'   => $tienda->id,
            ],
            [
                'precio' => $datos['precio'],
            ]
        );

        // Redirige al listado con mensaje de éxito.
        return redirect()
            ->route('precios.index', $producto)
            ->with('success', 'Precio guardado correctamente');
    }

    /**
     * Muestra el formulario de edición para un registro de precio existente.
     *
     * @param Precio $precio Instancia del precio a editar.
     * @return View
     */
    public function edit(Precio $precio): View
    {
        // Comprueba permisos sobre la tienda.
        $this->comprobarPermisoTienda($precio);

        // Devuelve la vista de edición.
        return view(
            'precios.edit',
            compact('precio')
        );
    }

    /**
     * Actualiza el valor de un registro de precio existente en la base de datos.
     *
     * @param Request $request Solicitud HTTP con el nuevo valor del precio.
     * @param Precio $precio Instancia del precio a actualizar.
     * @return RedirectResponse
     */
    public function update(Request $request, Precio $precio): RedirectResponse
    {
        // Verifica permisos.
        $this->comprobarPermisoTienda($precio);

        // Valida el nuevo precio.
        $datos = $request->validate([
            'precio' => 'required|numeric|min:0',
        ]);

        // Actualiza el precio.
        $precio->update([
            'precio' => $datos['precio'],
        ]);

        // Redirige con mensaje de éxito.
        return redirect()
            ->route('precios.index', $precio->producto)
            ->with('success', 'Precio actualizado correctamente');
    }
    /**
     * Elimina un registro de precio existente previa comprobación de permisos sobre la tienda.
     *
     * @param Precio $precio Instancia del precio a eliminar.
     * @return RedirectResponse
     */
    public function destroy(Precio $precio): RedirectResponse
    {
        // Verifica permisos.
        $this->comprobarPermisoTienda($precio);

        // Guarda referencia al producto.
        $producto = $precio->producto;

        // Elimina el precio.
        $precio->delete();

        // Redirige con mensaje de éxito.
        return redirect()
            ->route('precios.index', $producto)
            ->with('success', 'Precio eliminado correctamente');
    }

    /**
     * Comprueba si el usuario autenticado tiene permisos sobre la tienda asociada al precio.
     *
     * @param Precio $precio Instancia del precio cuya tienda se va a verificar.
     * @return bool
     */
    private function comprobarPermisoTienda(Precio $precio): bool
    {
        // Usuario autenticado.
        $user = auth()->user();

        // Administrador tiene acceso total.
        if ($user->hasRole('administrador')) {
            return true;
        }

        // Gestor solo puede acceder a sus tiendas.
        if (
            $user->hasRole('gestor')
            && $precio->tienda->user_id == $user->id
        ) {
            return true;
        }

        abort(403);
    }
}
