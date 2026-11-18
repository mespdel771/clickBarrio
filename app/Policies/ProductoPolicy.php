<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;

/**
 * Política de autorización que centraliza los permisos de acceso y gestión sobre los productos del catálogo.
 */
class ProductoPolicy
{
    /**
     * Determina si el usuario puede visualizar el listado general de productos.
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Usuarios con roles de administrador, gestor y usuario pueden listar productos.
        return $user->hasAnyRole([
            'administrador',
            'gestor',
            'usuario',
        ]);
    }

    /**
     * Determina si el usuario puede visualizar el detalle de un producto concreto:
     * - Administrador: acceso a cualquier producto.
     * - Gestor: acceso únicamente a productos con stock en sus tiendas.
     * - Usuario: acceso únicamente a productos con existencias disponibles.
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @param Producto $producto Instancia del producto que se intenta consultar.
     * @return bool
     */
    public function view(User $user, Producto $producto): bool
    {
        // El usuario con rol administrador puede ver cualquier producto.
        if ($user->hasRole('administrador')) {
            return true;
        }

        // El usuario con rol gestor solo puede ver productos de su propia tienda.
        if ($user->hasRole('gestor')) {
            return $producto->stocks()
                ->whereHas('tienda', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->exists();
        }

        // El usuario con rol usuario solo puede ver productos con stock.
        return $producto->stocks()
            ->where('stock', '>', 0)
            ->exists();
    }

    /**
     * Determina si el usuario puede crear nuevos productos globales (administrador)
     * o vincular productos al catálogo de sus tiendas (gestor).
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @return bool
     */
    public function create(User $user): bool
    {
        // Permitimos el acceso a administradores y gestores; el controlador aplica las restricciones por tienda.
        return $user->hasAnyRole(['administrador', 'gestor']);
    }

    /**
     * Determina si un usuario con rol gestor puede incorporar un producto existente a su catálogo
     * verificando que pertenezca a la misma categoría que alguna de sus tiendas.
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @param Producto $producto Instancia del producto que se desea añadir.
     * @return bool
     */
    public function addToCatalog(User $user, Producto $producto): bool
    {
        // Si no es gestor, no tiene permiso.
        if (!$user->hasRole('gestor')) {
            return false;
        }

        // El usuario con rol gestor solo puede añadir productos de la categoría asignada a su tienda.
        return Tienda::where('user_id', $user->id)
            ->where('categoria_id', $producto->categoria_id)
            ->exists();
    }

    /**
     * Determina si el usuario puede actualizar los datos o precios/stock de un producto:
     * - Administrador: puede modificar cualquier producto.
     * - Gestor: puede modificar productos correspondientes a la categoría de su tienda.
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @param Producto $producto Instancia del producto a modificar.
     * @return bool
     */
    public function update(User $user, Producto $producto): bool
    {
        // El usuario con rol administrador puede modificar cualquier producto.
        if ($user->hasRole('administrador')) {
            return true;
        }

        // El usuario con rol gestor puede modificar productos que coincidan con la categoría de su tienda.
        if ($user->hasRole('gestor')) {
            return Tienda::where('user_id', $user->id)
                ->where('categoria_id', $producto->categoria_id)
                ->exists();
        }

        // El resto de usuarios no puede modificar productos.
        return false;
    }

    /**
     * Determina si el usuario puede eliminar un producto por completo del sistema
     * (acceso exclusivo para administradores).
     *
     * @param User $user Usuario autenticado que realiza la acción.
     * @param Producto $producto Instancia del producto a eliminar.
     * @return bool
     */
    public function delete(User $user, Producto $producto): bool
    {
        // Solo el usuario con rol administrador puede eliminar productos de forma global.
        return $user->hasRole('administrador');
    }
}
