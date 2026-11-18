<?php

namespace App\Policies;

use App\Models\Tienda;
use App\Models\User;

/**
 * Política de autorización que centraliza los permisos de acceso, creación, edición y eliminación sobre las tiendas.
 */
class TiendaPolicy
{
    /**
     * Determina si el usuario puede acceder al listado general de tiendas.
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Los tres roles pueden consultar las tiendas.
        return $user->hasAnyRole([
            'administrador',
            'gestor',
            'usuario',
        ]);
    }

    /**
     * Determina si el usuario puede visualizar el detalle de una tienda concreta:
     * - Administrador: acceso a cualquier tienda.
     * - Gestor: acceso únicamente a las tiendas de su propiedad.
     * - Usuario: acceso únicamente a tiendas que dispongan de stock activo.
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @param Tienda $tienda Instancia de la tienda que se intenta consultar.
     * @return bool
     */
    public function view(User $user, Tienda $tienda): bool
    {
        // El usuario con rol administrador puede ver cualquier tienda.
        if ($user->hasRole('administrador')) {
            return true;
        }

        // El usuario con rol gestor solo puede ver su propia tienda.
        if ($user->hasRole('gestor')) {
            return $tienda->user_id === $user->id;
        }

        // El usuario con rol usuario solo puede ver tiendas que tengan stock disponible.
        if ($user->hasRole('usuario')) {
            return $tienda->stocks()
                ->where('stock', '>', 0)
                ->exists();
        }

        // Si no tiene un rol válido, no tiene permiso.
        return false;
    }

    /**
     * Determina si el usuario puede crear una nueva tienda (acceso exclusivo para administradores).
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @return bool
     */
    public function create(User $user): bool
    {
        // Solo el usuario con rol administrador puede crear una tienda.
        return $user->hasRole('administrador');
    }

    /**
     * Determina si el usuario puede modificar los datos de una tienda:
     * - Administrador: puede modificar cualquier tienda.
     * - Gestor: puede modificar únicamente las tiendas de su propiedad.
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @param Tienda $tienda Instancia de la tienda a modificar.
     * @return bool
     */
    public function update(User $user, Tienda $tienda): bool
    {
        // El usuario con rol administrador puede modificar cualquier tienda.
        if ($user->hasRole('administrador')) {
            return true;
        }

        // El usuario con rol gestor solo puede modificar su tienda.
        if ($user->hasRole('gestor')) {
            return $tienda->user_id === $user->id;
        }

        // El resto de usuarios no puede modificar tiendas.
        return false;
    }

    /**
     * Determina si el usuario puede eliminar una tienda (acceso exclusivo para administradores).
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @param Tienda $tienda Instancia de la tienda a eliminar.
     * @return bool
     */
    public function delete(User $user, Tienda $tienda): bool
    {
        // Solo el usuario con rol administrador puede borrar tiendas.
        return $user->hasRole('administrador');
    }
}
