<?php

namespace App\Http\Controllers;

use App\Mail\ReservaUsuarioMail;
use App\Models\Carrito;
use App\Models\Precio;
use App\Models\Reserva;
use App\Models\ReservaItem;
use App\Models\Stock;
use App\Models\Tienda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Controlador encargado de gestionar la consulta, detalle y creación de reservas,
 * así como el envío de notificaciones por correo electrónico tras finalizar una compra.
 */
class ReservaController extends Controller
{
    /**
     * Muestra el listado de reservas filtrado según el rol del usuario autenticado:
     * - Administrador: visualiza todas las reservas del sistema.
     * - Gestor: visualiza las reservas que incluyen productos de sus tiendas.
     * - Usuario: visualiza únicamente sus propias reservas.
     *
     * @return View
     */
    public function index(): View
    {
        // Autenticación del usuario.
        $user = auth()->user();

        // El administrador puede ver todas las reservas.
        if ($user->hasRole('administrador')) {
            $reservas = Reserva::with([
                'user',
                'items.producto',
                'items.tienda',
            ])->orderByDesc('created_at')->get();
        }
        // El gestor solo ve reservas relacionadas con su tienda o tiendas.
        elseif ($user->hasRole('gestor')) {
            // Obtiene las tiendas que pertenecen al gestor.
            $tiendas = Tienda::where('user_id', $user->id)->pluck('id');

            // Búsqueda de las reservas que contengan productos de esas tiendas.
            $reservas = Reserva::whereHas('items', function ($q) use ($tiendas) {
                $q->whereIn('tienda_id', $tiendas);
            })
            ->with(['user', 'items.producto', 'items.tienda'])
            ->orderByDesc('created_at')
            ->get();
        }
        // El cliente solo puede ver sus propias reservas.
        else {
            $reservas = Reserva::where('user_id', $user->id)
                ->with(['items.producto', 'items.tienda'])
                ->orderByDesc('created_at')
                ->get();
        }

        // Envía las reservas a la vista.
        return view('reservas.index', compact('reservas'));
    }

    /**
     * Muestra el detalle de una reserva verificando los permisos de acceso según el rol:
     * - Administrador: acceso total a cualquier reserva.
     * - Gestor: acceso únicamente si la reserva contiene artículos de sus tiendas.
     * - Usuario: acceso únicamente si es el propietario de la reserva.
     *
     * @param Reserva $reserva Instancia de la reserva cuyo detalle se desea consultar.
     * @return View
     */
    public function show(Reserva $reserva): View
    {
        // Autenticación del usuario.
        $user = auth()->user();

        // Control de acceso por roles.
        if ($user->hasRole('gestor')) {
            $misTiendasIds = Tienda::where('user_id', $user->id)->pluck('id')->toArray();
            $tieneItemsDeMisTiendas = $reserva->items()->whereIn('tienda_id', $misTiendasIds)->exists();

            if (!$tieneItemsDeMisTiendas) {
                abort(403, 'No tienes permisos para ver esta reserva.');
            }
        } elseif (!$user->hasRole('administrador') && $reserva->user_id !== $user->id) {
            abort(403, 'No tienes permisos para ver esta reserva.');
        }

        // Recupera los productos y tiendas relacionados.
        $reserva->load([
            'items.producto',
            'items.tienda',
        ]);

        // Envía la reserva a la vista de detalle.
        return view('reservas.show', compact('reserva'));
    }

    /**
     * Convierte el carrito actual del usuario en una reserva bajo transacción,
     * validando disponibilidad de stock y precios vigentes, descontando inventario
     * y enviando correos de confirmación tanto al usuario como a las tiendas implicadas.
     *
     * @return RedirectResponse
     *
     * @throws \Exception Si ningún producto del carrito tiene stock o precio válido disponible.
     */
       public function finalizarCompra(): RedirectResponse
    {
        // Obtiene el carrito del usuario con sus productos y tiendas.
        $carrito = Carrito::where('user_id', Auth::id())
            ->with(['items.producto', 'items.tienda'])
            ->first();

        // Si el carrito está vacío, vuelve al carrito.
        if (!$carrito || $carrito->items->isEmpty()) {
            return redirect()
                ->route('carrito.index')
                ->with('error', 'Carrito vacío.');
        }

        // Realiza toda la operación de compra dentro de una transacción.
        try {
            $resultado = DB::transaction(function () use ($carrito) {
                $itemsValidos = [];
                $itemsEliminados = [];
                $total = 0;

                // Comprueba cada producto del carrito.
                foreach ($carrito->items as $item) {
                    // Bloquea el stock para evitar cambios simultáneos.
                    $stock = Stock::where('producto_id', $item->producto_id)
                        ->where('tienda_id', $item->tienda_id)
                        ->lockForUpdate()
                        ->first();

                    // Elimina de la reserva los productos sin stock suficiente.
                    if (!$stock || $stock->stock < $item->cantidad) {
                        $itemsEliminados[] = $item;
                        continue;
                    }

                    // Obtiene el precio actual del producto en la tienda.
                    $precio = Precio::where('producto_id', $item->producto_id)
                        ->where('tienda_id', $item->tienda_id)
                        ->lockForUpdate()
                        ->first();

                    // Si no existe precio, el producto no puede reservarse.
                    if (!$precio) {
                        $itemsEliminados[] = $item;
                        continue;
                    }

                    // Calcula el importe usando el precio actual.
                    $precioFinal = $precio->precio;
                    $importe = $precioFinal * $item->cantidad;

                    // Guarda el producto como válido para la reserva.
                    $itemsValidos[] = [
                        'item'    => $item,
                        'precio'  => $precioFinal,
                        'importe' => $importe,
                    ];

                    // Calcula el importe total.
                    $total += $importe;
                }

                // Cancela la operación si ningún producto está disponible.
                if (empty($itemsValidos)) {
                    throw new \Exception('No hay productos disponibles.');
                }

                // Crea la reserva con el total calculado.
                $reserva = Reserva::create([
                    'user_id' => Auth::id(),
                    'total'   => $total,
                ]);

                // Guarda los productos y descuenta el stock.
                foreach ($itemsValidos as $data) {
                    $item = $data['item'];

                    // Guarda cada producto en la reserva.
                    ReservaItem::create([
                        'reserva_id'  => $reserva->id,
                        'tienda_id'   => $item->tienda_id,
                        'producto_id' => $item->producto_id,
                        'cantidad'    => $item->cantidad,
                        'precio'      => $data['precio'],
                        'importe'     => $data['importe'],
                    ]);

                    // Descuenta del stock la cantidad reservada.
                    Stock::where('producto_id', $item->producto_id)
                        ->where('tienda_id', $item->tienda_id)
                        ->decrement('stock', $item->cantidad);
                }

                // Vacía el carrito.
                $carrito->items()->delete();

                // Devuelve la reserva y los productos eliminados.
                return [
                    'reserva'    => $reserva,
                    'eliminados' => $itemsEliminados,
                ];
            });

            // Carga los datos necesarios para enviar los correos.
            $reserva = $resultado['reserva'];
            $reserva->load(['user', 'items.producto', 'items.tienda']);

            // Envío de correos protegido para no interrumpir la compra si falla el servidor SMTP.
            try {
                // Envío de email con la confirmación al usuario.
                Mail::to($reserva->user->email)
                    ->send(new \App\Mail\ReservaUsuarioMail($reserva));

                // Pausa de 1 segundo para respetar el límite por segundo de Mailtrap.
                sleep(1);

                // Envío de email con la confirmación a cada tienda implicada.
                $porTienda = $reserva->items->groupBy('tienda_id');

                foreach ($porTienda as $items) {
                    $tienda = $items->first()->tienda;

                    // Solo envía el correo si la tienda tiene email.
                    if ($tienda && $tienda->email) {
                        Mail::to($tienda->email)
                            ->send(new \App\Mail\ReservaTiendaMail($items, $tienda, $reserva));

                        sleep(1);
                    }
                }
            } catch (\Throwable $mailError) {
                \Illuminate\Support\Facades\Log::error('Error enviando correos de reserva #' . $reserva->id . ': ' . $mailError->getMessage());
            }

            $msg = 'Reserva realizada correctamente.';

            // Informa si algunos productos fueron eliminados por falta de stock.
            if (!empty($resultado['eliminados'])) {
                $msg .= ' Algunos productos sin stock fueron eliminados.';
            }

            // Redirige a la reserva creada.
            return redirect()
                ->route('reservas.show', $reserva->id)
                ->with('success', $msg);

        } catch (\Throwable $e) {
            // Si ocurre un error en la transacción, vuelve al carrito mostrando el mensaje.
            return redirect()
                ->route('carrito.index')
                ->with('error', 'No se pudo procesar la compra: ' . $e->getMessage());
        }
    }
}
