<?php

namespace App\Http\Controllers;
// CobrosController.php
use App\Services\FacturaService;
use Illuminate\Http\Request;
use App\Models\Factura;

class CobrosController extends Controller
{
    public function index()
    {
        // La vista levanta los datos por AJAX desde /cobros/data
        return view('cobros');
    }

    public function data(Request $request)
    {
        $facturas = app(FacturaService::class)->listarFacturasCobros();
        return response()->json(['data' => $facturas]);
    }
}
