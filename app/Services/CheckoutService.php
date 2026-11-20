<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\Precio;
use App\Models\Reserva;
use App\Models\ReservaItem;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Servicio encargado de gestionar el proceso completo de reserva (checkout),
 * validando stock y precios bajo transacción, generando la reserva y descontando el inventario.
 */
class CheckoutService
{
    /**
     * Procesa la reserva completa de un carrito de reservas bajo una transacción de base de datos,
     * creando la reserva asociada, descontando el stock y vaciando el carrito.
     *
     * @param Carrito $carrito Instancia del carrito de compras que se va a procesar.
     * @return Reserva Instancia de la reserva generada tras completar la compra.
     *
     * @throws \Exception Si el carrito está vacío, no hay stock suficiente o algún producto carece de precio vigente.
     */
    public function process(Carrito $carrito): Reserva
    {
        // Ejecuta todo el proceso dentro de una transacción; si algo falla, los cambios se deshacen.
        return DB::transaction(function () use ($carrito) {
            // Carga los productos y tiendas asociados a los artículos del carrito.
            $carrito->load(['items.producto', 'items.tienda']);

            // Comprueba que el carrito tenga productos.
            if ($carrito->items->isEmpty()) {
                throw new \Exception('El carrito está vacío.');
            }

            // Inicializa el importe total de la reserva.
            $total = 0;

            // Validación final (stock + precio). Recorre todos los artículos del carrito.
            foreach ($carrito->items as $item) {
                // Busca el stock del producto en la tienda correspondiente.
                $stock = Stock::where('producto_id', $item->producto_id)
                    ->where('tienda_id', $item->tienda_id)
                    ->lockForUpdate()
                    ->first();

                // Comprueba que exista stock suficiente.
                if (!$stock || $stock->stock < $item->cantidad) {
                    throw new \Exception(
                        'Stock insuficiente en ' . $item->producto->denominacion
                    );
                }

                // Busca el precio actual del producto en esa tienda.
                $precio = Precio::where('producto_id', $item->producto_id)
                    ->where('tienda_id', $item->tienda_id)
                    ->lockForUpdate()
                    ->first();

                // Comprueba que el producto tenga un precio definido.
                if (!$precio) {
                    throw new \Exception(
                        'El producto no tiene precio en ' . $item->tienda->nombre
                    );
                }

                // Guarda en el carrito el precio utilizado finalmente en la reserva.
                $item->precio = $precio->precio;
                // Calcula el importe del artículo: precio × cantidad.
                $item->importe = $precio->precio * $item->cantidad;
                $item->save();

                // Suma el importe del artículo al total de la reserva.
                $total += $item->importe;
            }

            // Crea la reserva asociada al usuario actual.
            $reserva = Reserva::create([
                'user_id' => Auth::id(),
                'total'   => $total,
            ]);

            // Recorre los artículos del carrito.
            foreach ($carrito->items as $item) {
                // Crea un registro con el detalle de cada producto reservado.
                ReservaItem::create([
                    'reserva_id'  => $reserva->id,
                    'tienda_id'   => $item->tienda_id,
                    'producto_id' => $item->producto_id,
                    'cantidad'    => $item->cantidad,
                    'precio'      => $item->precio,
                    'importe'     => $item->importe,
                ]);

                // Reduce del stock la cantidad reservada.
                Stock::where('producto_id', $item->producto_id)
                    ->where('tienda_id', $item->tienda_id)
                    ->decrement('stock', $item->cantidad);
            }

            // Elimina los artículos del carrito una vez creada la reserva.
            $carrito->items()->delete();

            // Devuelve la reserva creada.
            return $reserva;
        });
    }
}
