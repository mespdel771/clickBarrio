<?php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;

/**
 * Política de autorización que centraliza los permisos de acceso, descarga y gestión sobre las reservas.
 */
class ReservaPolicy
{
    /**
     * Determina si el usuario puede acceder al listado general de reservas.
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Los tres roles pueden consultar el listado.
        return $user->hasAnyRole([
            'administrador',
            'gestor',
            'usuario',
        ]);
    }

    /**
     * Determina si el usuario puede visualizar el detalle de una reserva concreta:
     * - Administrador: acceso a cualquier reserva.
     * - Usuario: acceso únicamente a sus propias reservas.
     * - Gestor: acceso únicamente a las reservas que incluyen productos de sus tiendas.
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @param Reserva $reserva Instancia de la reserva que se intenta consultar.
     * @return bool
     */
    public function view(User $user, Reserva $reserva): bool
    {
        // El usuario con rol administrador puede ver cualquier reserva.
        if ($user->hasRole('administrador')) {
            return true;
        }

        // El usuario con rol usuario solo puede ver sus propias reservas.
        if ($user->hasRole('usuario')) {
            return $reserva->user_id === $user->id;
        }

        // El usuario con rol gestor solo puede ver reservas de sus tiendas.
        if ($user->hasRole('gestor')) {
            // Comprueba si alguna tienda de la reserva pertenece al gestor.
            return $reserva->items
                ->pluck('tienda.user_id')
                ->contains($user->id);
        }

        // Si no tiene un rol válido, no tiene permiso.
        return false;
    }

    /**
     * Determina si el usuario puede descargar el comprobante en PDF de una reserva,
     * aplicando las mismas reglas de acceso que para su visualización.
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @param Reserva $reserva Instancia de la reserva cuyo PDF se solicita.
     * @return bool
     */
    public function download(User $user, Reserva $reserva): bool
    {
        // Utiliza los mismos permisos que para ver la reserva.
        return $this->view($user, $reserva);
    }

    /**
     * Determina si el usuario puede eliminar una reserva (acceso exclusivo para administradores).
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @param Reserva $reserva Instancia de la reserva a eliminar.
     * @return bool
     */
    public function delete(User $user, Reserva $reserva): bool
    {
        // Solo el administrador puede eliminar reservas.
        return $user->hasRole('administrador');
    }
}
