<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Models\UserLogin;

class LogFailedLogin
{
    public function handle(Failed $event)
    {
        // Crear un registro de log de usuario con estado 0 (login fallido)
 if ($event->user) {
        UserLogin::create([
            'user_id' => $event->user ? $event->user->id : null,  // Si existe un usuario (en caso de login fallido)
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'estado' => 0,  // Estado 0 cuando el login falla
        ]);
         }
    }
}
