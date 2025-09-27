<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\ArticuloFactura;
use App\Models\Articulo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\FacturaService;

class FacturasController extends Controller
{
    public function __construct(private FacturaService $facturaService) {}

    public function data()
    {
        $facturas = $this->facturaService->listarFacturasResumen();
        return response()->json(['data' => $facturas]);
    }

    public function index()
    {
        $facturas = Factura::all();
        return view('facturas', compact('facturas'));
    }

    public function crear()
    {
        return view('crear_factura');
    }

    public function store(Request $request)
    {
        // Normalizar array de artículos (viene como JSON string)
        $request->merge([
            'senar' => $request->has('senar') ? 1 : 0,
            'articulos' => json_decode($request->input('articulos'), true),
        ]);

        // Validación (incluye descuento %)
        $request->validate([
            'id_cliente'                   => 'required|exists:clientes,id',
            'vendedor_id'                   => 'nullable|exists:vendedores,empleado_id',
            'numero'                       => 'required|integer|unique:facturas,numero',
            'fecha'                        => 'required|date',
            'fecha_vencimiento'            => 'nullable|date',
            'condiciones'                  => 'nullable|string',
            'nombre'                       => 'required|string',
            'articulos'                    => 'required|array|min:1',
            'articulos.*.id_articulo'      => 'nullable|exists:articulos,id',
            'articulos.*.nombre'           => 'required|string',
            'articulos.*.cantidad'         => 'required|integer|min:1',
            'articulos.*.precio'           => 'required|numeric|min:0',
            'articulos.*.descuento'        => 'nullable|numeric|min:0|max:100', // << agregado
            'articulos.*.impuesto'         => 'required|numeric|min:0|max:100',
            'articulos.*.descripcion'      => 'nullable|string',

            // 👇 reglas para seña
            'senar'                        => 'sometimes|boolean',
            'sena_factura_porcentaje'      => 'required_if:senar,1|nullable|numeric|min:0.01|max:100',
        ]);

        // Asegurar que cada artículo tenga descuento por defecto 0
        $articulos = collect($request->articulos)->map(function ($a) {
            $a['descuento'] = isset($a['descuento']) ? (float)$a['descuento'] : 0.0;
            return $a;
        })->all();

        $enviar = (int) $request->input('enviar', 0);

        try {
            // Payload para el service (incluye descuento por artículo)
            $payload = [
                'factura' => [
                    'id_cliente'        => $request->id_cliente,
                    'vendedor_id'        => $request->vendedor_id ?? null,
                    'nombre'            => $request->nombre,
                    'apellido1'         => $request->apellido1 ?? null,
                    'apellido2'         => $request->apellido2 ?? null,
                    'telefono'          => $request->telefono ?? null,
                    'email'             => $request->email ?? null,
                    'tipo_cliente'      => $request->tipo_cliente ?? null,
                    'nif'               => $request->nif ?? null,
                    'codigo_postal'     => $request->codigo_postal ?? null,
                    'pais'              => $request->pais ?? null,
                    'provincia'         => $request->provincia ?? null,
                    'localidad'         => $request->localidad ?? null,
                    'numero'            => $request->numero,
                    'fecha'             => $request->fecha,
                    'fecha_vencimiento' => $request->fecha_vencimiento ?? null,
                    'condiciones'       => $request->condiciones ?? null,
                    // 👇 datos de seña
                    'senar'                   => $request->boolean('senar'),
                    'sena_factura_porcentaje' => $request->input('sena_factura_porcentaje'),
                ],
                'articulos' => $articulos, // ← con 'descuento'
            ];

            // Envío de mail post-commit
            $afterCommit = function (Factura $factura) use ($enviar) {
                if ($enviar !== 1 || empty($factura->email)) return;

                $pdf = $this->generarPdfFactura($factura);
                Mail::send([], [], function ($message) use ($factura, $pdf) {
                    $message->to($factura->email)
                        ->subject("Factura N° {$factura->numero}")
                        ->attachData($pdf->output(), "factura_{$factura->numero}.pdf")
                        ->html("Adjunto encontrará la factura generada.");
                });
            };

            $factura = $this->facturaService->crearFactura($payload, $afterCommit);

            return response()->json([
                'success'    => true,
                'message'    => 'Factura creada correctamente',
                'factura_id' => $factura->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la factura',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $factura = Factura::with('articuloFacturas')->findOrFail($id);

        try {
            $this->facturaService->eliminarFactura($factura);

            return response()->json(['message' => 'Factura eliminada correctamente.']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error al eliminar la factura',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function generarPdfFactura(Factura $factura)
    {
        // Asegurar que las relaciones estén cargadas (incluye pivote con descuento)
        $factura->loadMissing(['cliente', 'articuloFacturas.articulo']);

        // Cargar logo y convertir a base64
        $path = public_path('images/logos/logo_white.png');
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64_logo = 'data:image/' . $type . ';base64,' . base64_encode($data);

        return Pdf::loadView('pdf.factura', [
            'factura'      => $factura,
            'base64_logo'  => $base64_logo,
        ])->setPaper('A4');
    }

    public function ver($id)
    {
        $factura = Factura::with(['cliente', 'articuloFacturas.articulo'])->findOrFail($id);
        $pdf = $this->generarPdfFactura($factura);
        return $pdf->stream("factura_{$factura->numero}.pdf");
    }
}