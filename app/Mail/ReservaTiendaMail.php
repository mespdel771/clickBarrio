<?php

namespace App\Mail;

use App\Models\Reserva;
use App\Models\ReservaItem;
use App\Models\Tienda;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Clase Mailable encargada de generar y enviar el correo electrónico
 * de notificación a una tienda cuando se recibe una nueva reserva, adjuntando
 * el comprobante en formato PDF.
 */
class ReservaTiendaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Colección de líneas de producto (artículos) correspondientes a la tienda.
     *
     * @var Collection<int, ReservaItem>
     */
    public $items;

    /**
     * Instancia de la tienda destinataria de la notificación.
     *
     * @var Tienda
     */
    public $tienda;

    /**
     * Instancia de la reserva realizada por el cliente.
     *
     * @var Reserva
     */
    public $reserva;

    /**
     * Crea una nueva instancia del mensaje de correo con los datos de la reserva.
     *
     * @param Collection<int, ReservaItem> $items Artículos reservados pertenecientes a la tienda.
     * @param Tienda $tienda Instancia de la tienda que recibe el pedido.
     * @param Reserva $reserva Instancia de la reserva completa.
     */
    public function __construct($items, $tienda, $reserva)
    {
        // Asegura que la relación 'producto' esté cargada en la colección de items
        if ($items instanceof \Illuminate\Database\Eloquent\Collection) {
            $items->loadMissing('producto');
        }

        $this->items = $items;
        $this->tienda = $tienda;
        $this->reserva = $reserva;
    }

    /**
     * Define el sobre (asunto y metadatos) del correo electrónico.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva reserva recibida - Nº ' . $this->reserva->id,
        );
    }

    /**
     * Define la vista Blade que renderiza el contenido del correo electrónico.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reserva_tienda',
        );
    }

    /**
     * Genera el comprobante de reserva en PDF mediante DomPDF y lo adjunta al correo.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView(
            'pdf.reserva_tienda',
            [
                'items'   => $this->items,
                'tienda'  => $this->tienda,
                'reserva' => $this->reserva,
            ]
        );

        // Devuelve el PDF como archivo adjunto.
        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'reserva_tienda_' . $this->reserva->id . '.pdf'
            )->withMime('application/pdf'), // Indica que el archivo es un PDF.
        ];
    }
}
