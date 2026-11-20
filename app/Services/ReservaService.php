<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\Precio;
use App\Models\Reserva;
use App\Models\ReservaItem;
use App\Models\Stock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Servicio encargado de gestionar la creación de reservas a partir de un carrito de compras,
 * validando stock y precios vigentes bajo transacción y descontando el inventario correspondiente.
 */
class ReservaService
{
    /**
     * Crea una reserva utilizando los productos válidos añadidos al carrito bajo transacción,
     * descartando aquellos sin stock o precio disponible y descontando las unidades reservadas.
     *
     * @param Carrito $carrito Instancia del carrito de reservas a procesar.
     * @return array{reserva: Reserva, eliminados: Collection<int, mixed>} Arreglo con la reserva generada y los artículos descartados.
     *
     * @throws \Exception Si ningún producto del carrito dispone de stock suficiente o precio activo.
     */
    public function crearDesdeCarrito(Carrito $carrito): array
    {
        // Ejecuta todas las operaciones dentro de una transacción.
        return DB::transaction(function () use ($carrito) {
            // Carga los productos y tiendas relacionados con los elementos del carrito.
            $carrito->load(['items.producto', 'items.tienda']);

            // Colección donde se guardarán los productos disponibles.
            $itemsValidos = collect();

            // Colección donde se guardarán los productos que no puedan reservarse.
            $itemsEliminados = collect();

            // Inicializa el importe total de la reserva.
            $total = 0;

            // Recorre todos los productos incluidos en el carrito.
            foreach ($carrito->items as $item) {
                // Busca el stock del producto en la tienda correspondiente; lockForUpdate bloquea el registro para evitar modificaciones simultáneas.
                $stock = Stock::where('producto_id', $item->producto_id)
                    ->where('tienda_id', $item->tienda_id)
                    ->lockForUpdate()
                    ->first();

                // Comprueba que exista stock suficiente.
                if (!$stock || $stock->stock < $item->cantidad) {
                    // Añade el producto a la lista de elementos eliminados.
                    $itemsEliminados->push($item);

                    // Pasa al siguiente producto del carrito.
                    continue;
                }

                // Busca el precio actual del producto en esa tienda.
                $precio = Precio::where('producto_id', $item->producto_id)
                    ->where('tienda_id', $item->tienda_id)
                    ->lockForUpdate()
                    ->first();

                // Comprueba que el producto tenga un precio disponible.
                if (!$precio) {
                    $itemsEliminados->push($item);
                    continue;
                }

                // Calcula el importe multiplicando precio por cantidad.
                $importe = $precio->precio * $item->cantidad;

                // Guarda los datos del producto válido.
                $itemsValidos->push([
                    'item'    => $item,
                    'precio'  => $precio->precio,
                    'importe' => $importe,
                ]);

                // Añade el importe del producto al total de la reserva.
                $total += $importe;
            }

            // Comprueba si no queda ningún producto disponible.
            if ($itemsValidos->isEmpty()) {
                // Cancela la operación si no hay productos disponibles.
                throw new \Exception('No hay productos disponibles.');
            }

            // Crea la reserva asociada al usuario autenticado.
            $reserva = Reserva::create([
                'user_id' => Auth::id(),
                'total'   => $total,
            ]);

            // Recorre todos los productos válidos para crear la reserva.
            foreach ($itemsValidos as $data) {
                $item = $data['item'];

                ReservaItem::create([
                    'reserva_id'  => $reserva->id,
                    'tienda_id'   => $item->tienda_id,
                    'producto_id' => $item->producto_id,
                    'cantidad'    => $item->cantidad,
                    'precio'      => $data['precio'],
                    'importe'     => $data['importe'],
                ]);

                // Reduce del stock la cantidad reservada.
                Stock::where('producto_id', $item->producto_id)
                    ->where('tienda_id', $item->tienda_id)
                    ->decrement('stock', $item->cantidad);
            }

            // Vacía todos los elementos del carrito después de crear la reserva.
            $carrito->items()->delete();

            // Devuelve la reserva creada y los productos que no pudieron reservarse.
            return [
                'reserva'    => $reserva,
                'eliminados' => $itemsEliminados,
            ];
        });
    }
}
