<?php

namespace App\Mail;

use App\Models\Reserva;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Clase Mailable encargada de generar y enviar el correo electrónico de confirmación
 * de compra al usuario, adjuntando el comprobante de la reserva en formato PDF.
 */
class ReservaUsuarioMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Instancia de la reserva que se detallará en el correo y en el comprobante PDF.
     *
     * @var Reserva
     */
    public $reserva;

    /**
     * Crea una nueva instancia del mensaje de correo con la reserva a confirmar.
     *
     * @param Reserva $reserva Instancia de la reserva realizada por el usuario.
     */
    public function __construct(Reserva $reserva)
    {
        $this->reserva = $reserva;
    }

    /**
     * Construye el correo electrónico configurando el asunto, la vista y el PDF adjunto.
     *
     * @return $this
     */
    public function build()
    {
        // Carga previamente los items, sus productos y la tienda de cada línea
        $this->reserva->loadMissing('items.producto', 'items.tienda');

        // Genera el PDF utilizando la vista y los datos de la reserva.
        $pdf = Pdf::loadView(
            'pdf.reserva_usuario',
            [
                'reserva' => $this->reserva,
            ]
        );

        // Configura el correo, su vista y el archivo adjunto.
        return $this
            ->subject('Confirmación de tu compra')
            ->view('emails.reserva_usuario')
            ->attachData(
                $pdf->output(),
                'reserva_' . $this->reserva->id . '.pdf',
                [
                    'mime' => 'application/pdf',
                ]
            );
    }
}
