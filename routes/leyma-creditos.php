<?php

use App\Http\Controllers\ClienteCreditoController;
use App\Http\Controllers\CreditoDashboardController;
use App\Http\Controllers\GastoLeymaController;
use App\Http\Controllers\InyeccionController;
use App\Http\Controllers\LeymaCreditoController;
use App\Http\Controllers\PrestamoFamiliarController;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | Leyma Créditos Routes
 * |--------------------------------------------------------------------------
 * |
 * | Aquí se definen las rutas para el sistema de Leyma Créditos
 * |
 */

// Rutas principales del sistema
Route::middleware(['auth', 'permission:leyma_creditos'])->group(function () {
    Route::get('/leyma-creditos', function () {
        return view('leyma_creditos');
    })->name('leyma-creditos.index');

    // API endpoints
    Route::prefix('api/leyma-creditos')->group(function () {
        Route::get('/caja', [LeymaCreditoController::class, 'caja'])->name('leyma-creditos.caja');
        Route::get('/cierre', [LeymaCreditoController::class, 'cierre'])->name('leyma-creditos.cierre');
        Route::get('/estadisticas', [LeymaCreditoController::class, 'estadisticas'])->name('leyma-creditos.estadisticas');
        Route::get('/configuraciones', [LeymaCreditoController::class, 'configuraciones'])->name('leyma-creditos.configuraciones');
        Route::post('/calcular-porcentaje', [LeymaCreditoController::class, 'calcularPorcentaje'])->name('leyma-creditos.calcular-porcentaje');
        Route::post('/aplicar-recargo', [LeymaCreditoController::class, 'aplicarRecargo'])->name('leyma-creditos.aplicar-recargo');
        Route::post('/aplicar-descuento', [LeymaCreditoController::class, 'aplicarDescuento'])->name('leyma-creditos.aplicar-descuento');
        Route::post('/registrar-pago', [LeymaCreditoController::class, 'registrarPago'])->name('leyma-creditos.registrar-pago');
        Route::get('/flujo-mensual', [LeymaCreditoController::class, 'flujoMensual'])->name('leyma-creditos.flujo-mensual');
        Route::get('/tendencia-caja', [LeymaCreditoController::class, 'tendenciaCaja'])->name('leyma-creditos.tendencia-caja');
        Route::get('/resumen-periodos', [LeymaCreditoController::class, 'resumenPeriodos'])->name('leyma-creditos.resumen-periodos');
    });
});

// Rutas de clientes
Route::middleware(['auth', 'permission:leyma_clientes'])->group(function () {
    Route::get('/leyma-creditos/clientes', function () {
        return view('leyma_clientes');
    })->name('leyma-creditos.clientes.index');

    Route::prefix('api/leyma-creditos/clientes')->group(function () {
        Route::get('/data', [ClienteCreditoController::class, 'data'])->name('leyma-creditos.clientes.data');
        Route::get('/data-tom', [ClienteCreditoController::class, 'dataTom'])->name('leyma-creditos.clientes.data-tom');
        Route::get('/estadisticas', [ClienteCreditoController::class, 'estadisticas'])->name('leyma-creditos.clientes.estadisticas');
        Route::get('/{id}/historial-creditos', [ClienteCreditoController::class, 'historialCreditos'])->name('leyma-creditos.clientes.historial-creditos');
        Route::get('/{id}/saldos-favor', [ClienteCreditoController::class, 'saldosFavor'])->name('leyma-creditos.clientes.saldos-favor');
        Route::post('/', [ClienteCreditoController::class, 'store'])->name('leyma-creditos.clientes.store');
        Route::get('/{id}', [ClienteCreditoController::class, 'show'])->name('leyma-creditos.clientes.show');
        Route::put('/{id}', [ClienteCreditoController::class, 'update'])->name('leyma-creditos.clientes.update');
        Route::delete('/{id}', [ClienteCreditoController::class, 'destroy'])->name('leyma-creditos.clientes.destroy');
    });
});

// Rutas de créditos
Route::middleware(['auth', 'permission:leyma_creditos_gestion'])->group(function () {
    Route::get('/leyma-creditos/creditos', function () {
        return view('leyma_creditos_gestion');
    })->name('leyma-creditos.creditos.index');

    Route::prefix('api/leyma-creditos/creditos')->group(function () {
        Route::get('/data', [LeymaCreditoController::class, 'data'])->name('leyma-creditos.creditos.data');
        Route::get('/create', [LeymaCreditoController::class, 'create'])->name('leyma-creditos.creditos.create');
        Route::get('/estadisticas', [LeymaCreditoController::class, 'estadisticas'])->name('leyma-creditos.creditos.estadisticas');
        Route::get('/vencidos', [LeymaCreditoController::class, 'vencidos'])->name('leyma-creditos.creditos.vencidos');
        Route::get('/por-vencer', [LeymaCreditoController::class, 'porVencer'])->name('leyma-creditos.creditos.por-vencer');
        Route::post('/', [LeymaCreditoController::class, 'store'])->name('leyma-creditos.creditos.store');
        Route::get('/{id}', [LeymaCreditoController::class, 'show'])->name('leyma-creditos.creditos.show');
        Route::get('/{id}/pagos', [LeymaCreditoController::class, 'pagos'])->name('leyma-creditos.creditos.pagos');
        Route::put('/{id}', [LeymaCreditoController::class, 'update'])->name('leyma-creditos.creditos.update');
        Route::delete('/{id}', [LeymaCreditoController::class, 'destroy'])->name('leyma-creditos.creditos.destroy');

        // Rutas para gestión de pagos individuales
        Route::get('/pagos/{id}', [LeymaCreditoController::class, 'showPago'])->name('leyma-creditos.creditos.pagos.show');
        Route::post('/pagos/{id}/update', [LeymaCreditoController::class, 'updatePago'])->name('leyma-creditos.creditos.pagos.update');
        Route::post('/pagos/{id}/delete', [LeymaCreditoController::class, 'destroyPago'])->name('leyma-creditos.creditos.pagos.destroy');
    });
});

// Rutas de gastos
Route::middleware(['auth', 'permission:leyma_gastos'])->group(function () {
    Route::get('/leyma-creditos/gastos', function () {
        return view('leyma_gastos');
    })->name('leyma-creditos.gastos.index');

    Route::prefix('api/leyma-creditos/gastos')->group(function () {
        Route::get('/data', [GastoLeymaController::class, 'data'])->name('leyma-creditos.gastos.data');
        Route::get('/resumen-categorias', [GastoLeymaController::class, 'resumenCategorias'])->name('leyma-creditos.gastos.resumen-categorias');
        Route::get('/total-periodo', [GastoLeymaController::class, 'totalPeriodo'])->name('leyma-creditos.gastos.total-periodo');
        Route::post('/', [GastoLeymaController::class, 'store'])->name('leyma-creditos.gastos.store');
        Route::get('/{id}', [GastoLeymaController::class, 'show'])->name('leyma-creditos.gastos.show');
        Route::put('/{id}', [GastoLeymaController::class, 'update'])->name('leyma-creditos.gastos.update');
        Route::delete('/{id}', [GastoLeymaController::class, 'destroy'])->name('leyma-creditos.gastos.destroy');
    });
});

// Rutas de inyecciones
Route::middleware(['auth', 'permission:leyma_inyecciones'])->group(function () {
    Route::get('/leyma-creditos/inyecciones', function () {
        return view('leyma_inyecciones');
    })->name('leyma-creditos.inyecciones.index');

    Route::prefix('api/leyma-creditos/inyecciones')->group(function () {
        Route::get('/data', [InyeccionController::class, 'data'])->name('leyma-creditos.inyecciones.data');
        Route::get('/total-periodo', [InyeccionController::class, 'totalPeriodo'])->name('leyma-creditos.inyecciones.total-periodo');
        Route::post('/', [InyeccionController::class, 'store'])->name('leyma-creditos.inyecciones.store');
        Route::get('/{id}', [InyeccionController::class, 'show'])->name('leyma-creditos.inyecciones.show');
        Route::put('/{id}', [InyeccionController::class, 'update'])->name('leyma-creditos.inyecciones.update');
        Route::delete('/{id}', [InyeccionController::class, 'destroy'])->name('leyma-creditos.inyecciones.destroy');
    });
});

// Rutas de préstamos familiares
Route::middleware(['auth', 'permission:leyma_prestamos_familiares'])->group(function () {
    Route::get('/leyma-creditos/prestamos-familiares', function () {
        return view('leyma_prestamos_familiares');
    })->name('leyma-creditos.prestamos-familiares.index');

    Route::prefix('api/leyma-creditos/prestamos-familiares')->group(function () {
        Route::get('/data', [PrestamoFamiliarController::class, 'data'])->name('leyma-creditos.prestamos-familiares.data');
        Route::get('/total-periodo', [PrestamoFamiliarController::class, 'totalPeriodo'])->name('leyma-creditos.prestamos-familiares.total-periodo');
        Route::get('/vencidos', [PrestamoFamiliarController::class, 'vencidos'])->name('leyma-creditos.prestamos-familiares.vencidos');
        Route::post('/', [PrestamoFamiliarController::class, 'store'])->name('leyma-creditos.prestamos-familiares.store');
        Route::get('/{id}', [PrestamoFamiliarController::class, 'show'])->name('leyma-creditos.prestamos-familiares.show');
        Route::post('/{id}/update', [PrestamoFamiliarController::class, 'update'])->name('leyma-creditos.prestamos-familiares.update');
        Route::post('/{id}/delete', [PrestamoFamiliarController::class, 'destroy'])->name('leyma-creditos.prestamos-familiares.destroy');
    });
});

// API del Dashboard de Créditos
Route::middleware(['auth', 'permission:leyma_creditos'])->group(function () {
    Route::prefix('api/leyma-creditos/dashboard')->group(function () {
        Route::get('/estadisticas', [CreditoDashboardController::class, 'estadisticas'])->name('leyma-creditos.dashboard.estadisticas');
        Route::get('/graficos', [CreditoDashboardController::class, 'datosGraficos'])->name('leyma-creditos.dashboard.graficos');
    });
});
