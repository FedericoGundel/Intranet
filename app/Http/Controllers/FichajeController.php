<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fichaje; // Asumiendo que tienes un modelo Fichaje
use Carbon\Carbon;
use Auth;

class FichajeController extends Controller
{
    // app/Http/Controllers/FichajeController.php
private function fichajeActivo($userId): ?Fichaje
{
    return Fichaje::where('user_id', $userId)->whereNull('fecha_salida')->latest('fecha_entrada')->first();
}

public function index()
{
    $uid = auth()->id();
    $f = Fichaje::where('user_id',$uid)->whereNull('fecha_salida')->latest('fecha_entrada')->first();

    return view('fichaje', [
        'fichaje' => $f,         // puede ser null
        'estado'  => $f?->estado // trabajando|pausado|finalizado (finalizado no aplica en activo)
    ]);
}

public function entrada(Request $r)
{
    $uid = auth()->id();
    if ($this->fichajeActivo($uid)) {
        return response()->json(['success'=>false,'message'=>'Ya tenés una jornada abierta.'], 422);
    }

    $f = Fichaje::create([
        'user_id'        => $uid,
        'fecha_entrada'  => now(),
        'ultima_entrada' => now(),
        'tiempo_descanso'=> 0,
        'localizacion'   => $r->input('localizacion'),
    ]);

    return response()->json(['success'=>true,'message'=>'Entrada registrada','id'=>$f->id]);
}
public function pausa(Request $r)
{
    $uid = auth()->id();
    $f = $this->fichajeActivo($uid);
    if (!$f) return response()->json(['success'=>false,'message'=>'No hay jornada activa.'], 422);
    if ($f->estado !== 'trabajando') return response()->json(['success'=>false,'message'=>'Ya estás en pausa.'], 422);

    $f->update([
        'ultima_salida' => now(),
        'localizacion'  => $r->input('localizacion', $f->localizacion),
    ]);

    return response()->json(['success'=>true,'message'=>'Pausa registrada']);
}
public function reanudar(Request $r)
{
    $uid = auth()->id();
    $f = $this->fichajeActivo($uid);
    if (!$f) return response()->json(['success'=>false,'message'=>'No hay jornada activa.'], 422);
    if ($f->estado !== 'pausado') return response()->json(['success'=>false,'message'=>'No estás en pausa.'], 422);

    $descanso = $f->tiempo_descanso + $f->ultima_salida->diffInMinutes(now());

    $f->update([
        'tiempo_descanso' => $descanso,
        'ultima_entrada'  => now(),
        'localizacion'    => $r->input('localizacion', $f->localizacion),
    ]);

    return response()->json(['success'=>true,'message'=>'Reanudado']);
}
public function salida(Request $r)
{
    $uid = auth()->id();
    $f = $this->fichajeActivo($uid);
    if (!$f) return response()->json(['success'=>false,'message'=>'No hay jornada activa.'], 422);

    // si está pausado, cerrar la pausa sumando el descanso pendiente
    if ($f->estado === 'pausado') {
        $f->tiempo_descanso += $f->ultima_salida->diffInMinutes(now());
    }

    $f->fecha_salida = now();
    $f->localizacion = $r->input('localizacion', $f->localizacion);
    $f->save();

    return response()->json([
        'success'=>true,
        'message'=>'Salida registrada',
        'trabajado_min'=>$f->minutos_trabajados, // accessor
        'descanso_min'=>$f->tiempo_descanso,
    ]);
}


}
