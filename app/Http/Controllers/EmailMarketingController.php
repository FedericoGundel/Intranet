<?php

namespace App\Http\Controllers;

use App\Jobs\EnviarEmailMarketing;
use App\Models\Cliente;  // Asegúrate de tener el modelo Cliente creado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailMarketingController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();  // Puedes agregar filtros si hace falta
        return view('email_marketing', compact('clientes'));
    }

    public function enviar(Request $request)
    {
        $request->validate([
            'cliente_ids' => 'required|array|min:1',
            'asunto' => 'required|string|max:255',
            'cuerpo' => 'required|string',
            'archivos.*' => 'file|max:10240',  // 10 MB por archivo (opcional)
        ]);

        $archivos = [];

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $path = $archivo->store('adjuntos_email_temp');
                $archivos[] = storage_path('app/private/' . $path);
            }
        }

        $clientes = Cliente::whereIn('id', $request->cliente_ids)->get();

        foreach ($clientes as $cliente) {
            /*
             * Log::info('Despachando job EnviarEmailMarketing', [
             *     'cliente_id' => $cliente->id,
             *     'cliente_email' => $cliente->email,
             *     'asunto' => $request->asunto,
             *     'cuerpo' => $request->cuerpo,
             *     'adjuntos' => $archivos,
             * ]);
             */
            EnviarEmailMarketing::dispatch(
                $cliente->email,
                $request->asunto,
                $request->cuerpo,
                $archivos  // <--- los paths de los adjuntos
            );
        }

        return response()->json(['message' => 'Los emails están en cola para envío.']);
    }
}
