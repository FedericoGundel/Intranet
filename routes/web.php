<?php

use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\CargaArticuloController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CobrosController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\EmailMarketingController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\FacturasController;
use App\Http\Controllers\FichajeController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\PagosController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PlantillaEmailController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// 🔐 Todas estas rutas estarán protegidas por auth
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    // routes/web.php
    Route::get('/home/caja', [HomeController::class, 'caja']);

    Route::get('/home/facturas', [App\Http\Controllers\HomeController::class, 'facturas'])->name('home.facturas');
    Route::get('/clientes/data', [ClienteController::class, 'data'])->name('clientes.data');
    Route::get('/clientes/data-tom', [ClienteController::class, 'dataTom'])->name('clientes.data.tom');
    Route::get('/clientes/{id}', [ClienteController::class, 'show'])->name('clientes.show');
    Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::resource('clientes', ClienteController::class)->middleware('permission:clientes');

    Route::get('/articulos/data', [ArticuloController::class, 'data'])->name('articulos.data');
    Route::get('/articulos/data-tom', [ArticuloController::class, 'dataTom'])->name('articulos.data.tom');
    Route::get('/articulos/{id}', [ArticuloController::class, 'show'])->name('articulos.show');
    Route::put('/articulos/{id}', [ArticuloController::class, 'update'])->name('articulos.update');
    Route::delete('/articulos/{id}', [ArticuloController::class, 'destroy'])->name('articulos.destroy');
    Route::resource('articulos', ArticuloController::class)->middleware('permission:articulos');

    Route::get('/configuracion', [ConfigurationController::class, 'index'])->name('configuracion.index');
    Route::post('/configurations/update', [ConfigurationController::class, 'update'])->name('configurations.update');

    Route::get('/facturas/{factura}/pagos', [PagosController::class, 'index'])->name('facturas.pagos.index');
    Route::get('/facturas', [FacturasController::class, 'index'])->middleware('permission:facturas')->name('facturas.index');
    Route::get('/facturas/data', [FacturasController::class, 'data'])->name('facturas.data');
    Route::get('/crear_factura', [FacturasController::class, 'crear'])->middleware('permission:crear_factura')->name('facturas.crear');
    Route::post('/facturas', [FacturasController::class, 'store'])->name('facturas.store');
    Route::get('/facturas/{id}/ver', [FacturasController::class, 'ver'])->name('facturas.ver');
    Route::delete('/facturas/{id}', [FacturasController::class, 'destroy']);

    Route::get('/users/data', [UserController::class, 'data'])->name('users.data');
    Route::get('/users/pending', [UserController::class, 'pending'])->name('users.pending');
    Route::post('/users/{id}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::delete('/users/{id}/reject', [UserController::class, 'reject'])->name('users.reject');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/users', [UserController::class, 'index'])->middleware('permission:usuarios')->name('users.index');  // opcional

    Route::get('/email_marketing', [EmailMarketingController::class, 'index'])->middleware('permission:email_marketing')->name('email_marketing.index');
    Route::post('/email-marketing/enviar', [EmailMarketingController::class, 'enviar'])->name('email_marketing.enviar');

    Route::get('/plantilla_email/data-tom', [PlantillaEmailController::class, 'dataTom'])->name('plantilla_email.data.tom');
    Route::get('/plantilla_email/{id}', [PlantillaEmailController::class, 'show'])->name('plantilla_email.show');
    Route::put('/plantilla_email/{id}', [PlantillaEmailController::class, 'update'])->name('plantilla_email.update');
    Route::delete('/plantilla_email/{id}', [PlantillaEmailController::class, 'destroy'])->name('plantilla_email.destroy');
    Route::resource('plantilla_email', PlantillaEmailController::class);

    Route::get('/perfil/get/{id?}', [PerfilController::class, 'show'])->name('perfil.show');

    Route::get('/perfil/{id?}', [PerfilController::class, 'index'])->name('perfil.index');
    // Rutas para cambiar el fondo de perfil
    Route::post('/perfil', [PerfilController::class, 'updateFondo'])->name('perfil.update');

    // Ruta para obtener los registros de logs
    Route::get('/logs/data', [LogController::class, 'getData']);
    // En routes/web.php o routes/api.php
    Route::get('/fichaje', [FichajeController::class, 'index'])->middleware('permission:fichaje')->name('fichaje.index');
    Route::post('/fichaje/entrada', [FichajeController::class, 'entrada'])->name('fichaje.entrada');
    Route::post('/fichaje/pausa', [FichajeController::class, 'pausa'])->name('fichaje.pausa');
    Route::post('/fichaje/reanudar', [FichajeController::class, 'reanudar'])->name('fichaje.reanudar');
    Route::post('/fichaje/salida', [FichajeController::class, 'salida'])->name('fichaje.salida');

    Route::get('/cobros', [CobrosController::class, 'index'])->middleware('permission:cobros')->name('cobros.index');
    Route::get('/cobros/data', [CobrosController::class, 'data'])->name('cobros.data');
    // routes/web.php o api.php

    Route::get('/pagos', [PagosController::class, 'index'])->name('pagos.index');  // ?factura_id=123 opcional

    Route::post('/pagos', [PagosController::class, 'store'])->name('pagos.store');
    Route::delete('/pagos/{id}', [PagosController::class, 'destroy'])->name('pagos.destroy');
    // Opcional: restaurar
    Route::patch('/pagos/{id}/restore', [PagosController::class, 'restore'])->name('pagos.restore');
    Route::get('/pagos/{id}', [PagosController::class, 'show'])->name('pagos.show');
    Route::post('/pagos/{id}', [PagosController::class, 'update'])->name('pagos.update');

    Route::get('/roles', [RolesController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RolesController::class, 'store'])->name('roles.store');

    Route::get('/roles/permisos', [RolesController::class, 'permisos'])->name('roles.permisos');
    Route::get('/roles/all', [RolesController::class, 'allRoles'])->name('roles.all');
    Route::get('/roles/data-tom', [RolesController::class, 'dataTom'])->name('roles.data.tom');
    Route::get('/usuarios/{id}/roles', [RolesController::class, 'rolesDeUsuario'])
        ->name('usuarios.roles');
    Route::post('/usuarios/{id}/rol', [RolesController::class, 'setUserRole'])
        ->name('usuarios.rol.set');
    Route::get('/roles/{id}', [RolesController::class, 'show'])->name('roles.show');
    Route::post('/roles/{id}', [RolesController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RolesController::class, 'destroy'])->name('roles.destroy');

    Route::get('/carga-articulos/data', [CargaArticuloController::class, 'data']);
    Route::get('/carga-articulos/articulo/{id}', [CargaArticuloController::class, 'getByArticulo']);

    Route::post('/carga-articulos', [CargaArticuloController::class, 'store']);

    Route::delete('/carga-articulos/{carga}', [CargaArticuloController::class, 'destroy']);

    Route::get('/empleados/data', [EmpleadoController::class, 'data'])->name('empleados.data');
    Route::get('/empleados/data-tom', [EmpleadoController::class, 'dataTom'])->name('empleados.data.tom');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
    Route::get('/empleados/{empleado}/facturas', [EmpleadoController::class, 'ventas'])->name('empleados.ventas.');
    Route::get('/empleados/{id}/nominas', [NominaController::class, 'porEmpleado'])
        ->name('empleados.nominas');
    Route::resource('empleados', EmpleadoController::class);

    Route::get('/nominas/', [NominaController::class, 'index'])->name('nominas.index');  // listado resumido (opcional)
    Route::get('/nominas/data', [NominaController::class, 'data'])->name('nominas.data');

    Route::get('/nominas/preview', [NominaController::class, 'preview'])->name('preview');  // calcular sin guardar
    Route::post('/nominas/upsert', [NominaController::class, 'upsert'])->name('upsert');  // crear/actualizar borrador
    Route::post('/nominas/{id}/cerrar', [NominaController::class, 'cerrar'])->name('cerrar');  // cerrar nómina (congelar)
    Route::post('/nominas/{id}/pagar', [NominaController::class, 'pagar'])->name('pagar');  // registrar pago
    Route::post('/nominas/{id}/ajuste', [NominaController::class, 'agregarAjuste'])->name('ajuste');  // agregar ajuste manual
    Route::get('/nominas/{id}', [NominaController::class, 'show'])->name('show');  // detalle
    Route::delete('/nominas/{id}', [NominaController::class, 'destroy'])->name('destroy');

    Route::get('/gastos', [GastoController::class, 'index'])->name('gastos.index');
    Route::get('/gastos/data', [GastoController::class, 'data'])->name('gastos.data');
    Route::post('/gastos', [GastoController::class, 'store'])->name('gastos.store');
    Route::get('/gastos/{id}', [GastoController::class, 'show'])->name('gastos.show');
    Route::put('/gastos/{id}', [GastoController::class, 'update'])->name('gastos.update');
    Route::delete('/gastos/{id}', [GastoController::class, 'destroy'])->name('gastos.destroy');
});

// Incluir rutas de Leyma Créditos
require __DIR__ . '/leyma-creditos.php';
