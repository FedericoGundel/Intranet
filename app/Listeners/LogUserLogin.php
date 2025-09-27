<?php

namespace App\Listeners;


use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;
use App\Models\UserLogin;
use Illuminate\Support\Facades\Log;
class LogUserLogin
{
     public function handle(Login $event)
    {
        // Verificar que el usuario existe (esto se garantiza porque el evento Login solo se dispara cuando el login es exitoso)
        Log::info('Usuario que ha iniciado sesión:', [
            'user_id' => $event->user->id,
            'user_email' => $event->user->email,  // O cualquier otra información relevante
            'user_name' => $event->user->name,    // Si tienes el nombre del usuario
            'user_approved' => $event->user->approved,  // Estado de aprobación del usuario
        ]);
        
        if ($event->user) {
            // Verificamos si el usuario está aprobado
            if ($event->user->approved) {
                // Solo crear el registro de login si el usuario está aprobado
                UserLogin::create([
                    'user_id' => $event->user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'estado' => 1,  // Estado 1 cuando el login es exitoso y el usuario está aprobado
                ]);
            } else {
                // Si el usuario no está aprobado, registramos el intento de login fallido
                UserLogin::create([
                    'user_id' => $event->user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'estado' => 0,  // Estado 0 cuando el login falla (usuario no aprobado)
                ]);
            }
        }
    }
}
