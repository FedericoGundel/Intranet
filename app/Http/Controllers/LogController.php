<?php

namespace App\Http\Controllers;

use App\Models\UserLogin;  // Asegúrate de que el modelo esté importado
use Illuminate\Http\Request;

class LogController extends Controller
{
    // Método para obtener los datos de los logs
   public function getData()
    {
        // Obtener todos los registros de logs con la relación 'user' cargada
        $logs = UserLogin::with('user')->get();  // Cargar la relación 'user' (para obtener el nombre)

        // Obtener la ubicación de la IP (puedes usar un servicio de geolocalización como ipstack)
        foreach ($logs as $log) {
            // Obtener el nombre del usuario
            $log->user_name = $log->user ? $log->user->name : 'Desconocido';  // Asegurándote de que existe el usuario

            // Obtener la ubicación de la IP (usando un servicio externo, como ipstack, ipinfo.io, etc.)
          //  $log->ip_location = $this->getIpLocation($log->ip_address); 
         }

        // Retornar los logs con el nombre del usuario y la ubicación de la IP en formato JSON para DataTables
        return response()->json([
            'data' => $logs
        ]);
    }

    // Función para obtener la ubicación de la IP utilizando ipinfo.io (o cualquier otro servicio)
   // Función para obtener la ubicación de la IP utilizando ip-api.com (sin API Key)
private function getIpLocation($ip)
{
    // Llamada a la API de geolocalización gratuita
    $response = file_get_contents("http://ip-api.com/json/{$ip}");
    $data = json_decode($response);

    // Verificar si la API devolvió la ubicación
    if ($data && $data->status == 'success') {
        return "{$data->city}, {$data->regionName}, {$data->country}";
    }

    return 'Desconocido';  // Si no se encuentra la ubicación o la consulta falla
}

    // Otros métodos del controlador según sea necesario
}
