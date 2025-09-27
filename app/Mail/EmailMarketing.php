<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;

class EmailMarketing extends Mailable
{
    public $cliente;
    public $asunto;
    public $cuerpo;
    public $adjuntos;

    public function __construct($cliente, $asunto, $cuerpo, $adjuntos = [])
    {
        $this->cliente = $cliente;
        $this->asunto = $asunto;
        $this->cuerpo = $cuerpo;
        $this->adjuntos = $adjuntos;
    }

    public function build()
{
    $email = $this
        ->subject($this->asunto)
        ->html($this->cuerpo);

    foreach ($this->adjuntos as $adjunto) {
        if (file_exists($adjunto)) {
            Log::info("Adjuntando archivo: " . $adjunto);
            $email->attach($adjunto);
        } else {
            Log::warning("Archivo no encontrado para adjuntar: " . $adjunto);
        }
    }

    return $email;
}

}
