<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware encargado de comprobar si el usuario autentificado tiene autorización
 * para modificar un determinado recurso según su rol, permisos y propiedad.
 */
class Verificar
{
    /**
     * Procesa la solicitud entrante y decide si puede continuar o debe ser bloqueada (HTTP 403):
     * - Administradores: acceso directo sin restricciones.
     * - Gestores con permiso 'editar stock': acceso permitido únicamente si son propietarios del recurso.
     *
     * @param Request $request Solicitud HTTP entrante.
     * @param Closure(Request): (Response) $next Función que delega la petición al siguiente middleware o controlador.
     * @param string $parameterName Nombre del parámetro de ruta que identifica al recurso (ej. 'tienda' o 'producto').
     * @param string $foreignKey Nombre de la clave foránea que identifica al propietario en el modelo (por defecto 'user_id').
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $parameterName, string $foreignKey = 'user_id'): Response
    {
        $user = auth()->user();

        //  Si el usuario autentificado es administrador o tiene control total, paso libre directo.
        if ($user && $user->hasRole('administrador')) {
            return $next($request);
        }

        // 2. Obtiene el modelo de la URL (ej: tienda o producto).
        $model = $request->route($parameterName);

        // 3. Si el usuario es gestor, verificar que tenga el permiso y que sea el dueño.
        if ($user && $user->hasPermissionTo('editar stock') && $model) {
            $ownerId = is_object($model) ? $model->{$foreignKey} : $model;

            if ((int) $ownerId === (int) $user->id) {
                return $next($request);
            }
        }

        abort(403, 'No tienes autorización para modificar este recurso.');
    }
}
