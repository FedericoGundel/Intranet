<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\EmailMarketing;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\TextPart;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;

class EnviarEmailMarketing implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $clienteEmail;
  protected $asunto;
  protected $cuerpo;
  protected $archivos;

  public function __construct($clienteEmail, $asunto, $cuerpo, $archivos = [])
  {
    $this->clienteEmail = $clienteEmail;
    $this->asunto = $asunto;
    $this->cuerpo = $cuerpo;
    $this->archivos = $archivos;
  }

 public function handle()
{

 
 Mail::to($this->clienteEmail)
    ->send(new EmailMarketing(
        $this->clienteEmail, // este es el cliente
        $this->asunto,
        $this->cuerpo,
        $this->archivos
    ));

}
}
