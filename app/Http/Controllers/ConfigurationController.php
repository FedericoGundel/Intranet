<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfigurationController extends Controller
{
    public function index()
    {
        $configurations = Configuration::all()->pluck('value', 'key')->toArray();

        return view('configuracion', compact('configurations'));
    }

    public function update(Request $request)
    {
        $key = $request->input('key');

        if ($request->hasFile('imagen')) {
            $request->validate([
                'imagen' => 'image|max:2048', // máximo 2MB
            ]);

            // 1) Buscar valor anterior
            $oldUrl = Configuration::getValue($key); // <-- tu método para leer
            if ($oldUrl) {
                // Convertir /storage/... a path real en 'public'
                $oldPath = str_replace('/storage/', '', $oldUrl);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // 2) Guardar imagen nueva
            $path = $request->file('imagen')->store('config_images', 'public');
            $url = Storage::url($path);

            // 3) Guardar nueva URL en configuración
            Configuration::setValue($key, $url);

            return response()->json(['success' => true, 'url' => $url]);
        }

        // Para otros campos tipo texto
        $request->validate([
            'key' => 'required|string',
            'value' => 'nullable|string',
        ]);

        Configuration::setValue($request->key, $request->value);

        return response()->json(['success' => true]);
    }
}
