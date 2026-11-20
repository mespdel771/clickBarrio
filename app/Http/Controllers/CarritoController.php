<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\CarritoItem;
use App\Models\Precio;
use App\Models\Producto;
use App\Models\Reserva;
use App\Models\ReservaItem;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;

/**
 * Controlador encargado de gestionar el carrito de reservas del usuario,
 * la validación de stock y precios, y la modificación del carrito en reserva.
 */
class CarritoController extends Controller
{
    /**
     * Muestra la vista del carrito de reservas del usuario autentificado.
     *
     * @return View
     */
    public function index(): View
    {
        $carrito = $this->obtenerCarrito();

        $carrito->load([
            'items.producto',
            'items.tienda',
        ]);

        return view('carrito.index', compact('carrito'));
    }

    /**
     * Añade un producto al carrito comprobando previamente su disponibilidad de stock y precio en la tienda.
     * Calcula los importes sin persistir columnas calculadas en base de datos.
     *
     * @param Request $request Solicitud HTTP con producto_id, tienda_id y cantidad.
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tienda_id'   => 'required|exists:tiendas,id',
            'cantidad'    => 'required|integer|min:1',
        ]);

        $stock = Stock::where('producto_id', $request->producto_id)
            ->where('tienda_id', $request->tienda_id)
            ->first();

        if (!$stock || $stock->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Producto sin stock en esta tienda.',
            ], 422);
        }

        $carrito = $this->obtenerCarrito();

        $item = CarritoItem::where('carrito_id', $carrito->id)
            ->where('producto_id', $request->producto_id)
            ->where('tienda_id', $request->tienda_id)
            ->first();

        $cantidadActual = $item ? $item->cantidad : 0;
        $nuevaCantidad = $cantidadActual + $request->cantidad;

        if ($nuevaCantidad > $stock->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente. Disponible: ' . $stock->stock,
            ], 422);
        }

        $precio = Precio::where('producto_id', $request->producto_id)
            ->where('tienda_id', $request->tienda_id)
            ->first();

        if (!$precio) {
            return response()->json([
                'success' => false,
                'message' => 'Este producto no tiene precio en esta tienda.',
            ], 422);
        }

        if ($item) {
            $item->update([
                'cantidad' => $nuevaCantidad,
                'precio'   => $precio->precio,
            ]);
        } else {
            $item = CarritoItem::create([
                'carrito_id'  => $carrito->id,
                'producto_id' => $request->producto_id,
                'tienda_id'   => $request->tienda_id,
                'cantidad'    => $request->cantidad,
                'precio'      => $precio->precio,
            ]);
        }

        // Calcula el total acumulado del carrito (cantidad * precio)
        $total = $carrito->items()->sum(DB::raw('cantidad * precio'));
        $cantidadTotal = $carrito->items()->sum('cantidad');
        $importeItem = $item->cantidad * $item->precio;

        return response()->json([
            'success'  => true,
            'message'  => 'Producto añadido al carrito.',
            'cantidad' => $cantidadTotal,
            'item_id'  => $item->id,
            'importe'  => number_format($importeItem, 2, '.', ''),
            'total'    => number_format($total, 2, '.', ''),
        ]);
    }

    /**
     * Actualiza la cantidad de un artículo existente en el carrito verificando el stock disponible.
     *
     * @param Request $request Solicitud HTTP con la nueva cantidad solicitada.
     * @param CarritoItem $item Línea del carrito que se desea modificar.
     * @return JsonResponse
     */
    public function update(Request $request, CarritoItem $item): JsonResponse
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $carrito = $this->obtenerCarrito();

        if ($item->carrito_id != $carrito->id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado',
            ], 403);
        }

        $stock = Stock::where('producto_id', $item->producto_id)
            ->where('tienda_id', $item->tienda_id)
            ->first();

        if (!$stock || $request->cantidad > $stock->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente. Disponible: ' . ($stock->stock ?? 0),
            ], 422);
        }

        $item->update([
            'cantidad' => $request->cantidad,
        ]);

        // Calcula los importes al vuelo sin consultar columna 'importe'
        $total = $carrito->items()->sum(DB::raw('cantidad * precio'));
        $importeItem = $item->cantidad * $item->precio;

        return response()->json([
            'success'  => true,
            'message'  => 'Cantidad actualizada.',
            'cantidad' => $item->cantidad,
            'importe'  => number_format($importeItem, 2, '.', ''),
            'total'    => number_format($total, 2, '.', ''),
        ]);
    }

    /**
     * Elimina un artículo del carrito del usuario autentificado.
     *
     * @param CarritoItem $item Línea del carrito a eliminar.
     * @return JsonResponse
     */
    public function destroy(CarritoItem $item): JsonResponse
    {
        $carrito = $this->obtenerCarrito();

        if ($item->carrito_id != $carrito->id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado',
            ], 403);
        }

        $item->delete();

        // Calcula el nuevo total acumulado al vuelo
        $total = $carrito->items()->sum(DB::raw('cantidad * precio'));
        $cantidadTotal = $carrito->items()->sum('cantidad');

        return response()->json([
            'success'  => true,
            'message'  => 'Producto eliminado.',
            'cantidad' => $cantidadTotal,
            'total'    => number_format($total, 2, '.', ''),
        ]);
    }
    /**
     * Finaliza el proceso de reserva validando stock y precios bajo transacción,
     * generando la reserva y descontando el inventario correspondiente.
     *
     * @return RedirectResponse
     *
     * @throws \Exception Si algún producto no dispone de stock suficiente o carece de precio activo.
     */
    public function finalizar(): RedirectResponse
    {
        $carrito = $this->obtenerCarrito();

        $carrito->load(['items.producto', 'items.tienda']);

        if ($carrito->items->isEmpty()) {
            return redirect()
                ->route('carrito.index')
                ->with('error', 'Carrito vacío.');
        }

        try {
            $reserva = DB::transaction(function () use ($carrito) {
                // Validación final del stock y precio actualizado en cada tienda
                foreach ($carrito->items as $item) {
                    $stock = Stock::where('producto_id', $item->producto_id)
                        ->where('tienda_id', $item->tienda_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$stock || $stock->stock < $item->cantidad) {
                        throw new \Exception('Stock insuficiente en ' . $item->producto->denominacion);
                    }

                    $precio = Precio::where('producto_id', $item->producto_id)
                        ->where('tienda_id', $item->tienda_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$precio) {
                        throw new \Exception('El producto ya no tiene precio en ' . $item->tienda->nombre);
                    }

                    $item->precio = $precio->precio;
                    $item->save();
                }

                // Creación de la reserva (sin columna calculada 'total')
                $reserva = Reserva::create([
                    'user_id' => Auth::id(),
                ]);

                // Creación de los artículos de la reserva (sin columna calculada 'importe') y descuento de stock
                foreach ($carrito->items as $item) {
                    ReservaItem::create([
                        'reserva_id'  => $reserva->id,
                        'tienda_id'   => $item->tienda_id,
                        'producto_id' => $item->producto_id,
                        'cantidad'    => $item->cantidad,
                        'precio'      => $item->precio,
                    ]);

                    Stock::where('producto_id', $item->producto_id)
                        ->where('tienda_id', $item->tienda_id)
                        ->decrement('stock', $item->cantidad);
                }

                // Vaciar el carrito tras la reserva confirmada
                $carrito->items()->delete();

                return $reserva;
            });
                        // Carga las relaciones necesarias para construir los correos de confirmación
            $reserva->load(['user', 'items.producto', 'items.tienda']);

            // Envía correo electrónico de confirmación al cliente que realizó la reserva
            if ($reserva->user && $reserva->user->email) {
                \Illuminate\Support\Facades\Mail::to($reserva->user->email)
                    ->send(new \App\Mail\ReservaUsuarioMail($reserva));
            }

            // Agrupa los artículos por tienda y envía a cada establecimiento su aviso de pedido
            $porTienda = $reserva->items->groupBy('tienda_id');

            foreach ($porTienda as $items) {
                $tienda = $items->first()->tienda;

                // Solo envía el correo si la tienda dispone de dirección email configurada
                if ($tienda && $tienda->email) {
                    \Illuminate\Support\Facades\Mail::to($tienda->email)
                        ->send(new \App\Mail\ReservaTiendaMail($items, $tienda, $reserva));
                }
            }

            return redirect()
                ->route('reservas.show', $reserva->id)
                ->with('success', 'Compra finalizada correctamente y correos de confirmación enviados.');

        }
            catch (\Throwable $e) {
            return redirect()
                ->route('carrito.index')
                ->with('error', 'Error al procesar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene el carrito activo del usuario autentificado o crea uno nuevo si no existe.
     *
     * @return Carrito
     */
    private function obtenerCarrito(): Carrito
    {
        return Carrito::firstOrCreate([
            'user_id' => Auth::id(),
        ]);
    }
}
