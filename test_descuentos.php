<?php

require_once 'vendor/autoload.php';

use App\Models\Credito;

echo "=== PRUEBA DE DESCUENTOS EN CRÉDITOS ===\n\n";

$credito = Credito::with(['recargos', 'descuentos'])->first();

if (!$credito) {
    echo "No se encontró ningún crédito.\n";
    exit;
}

echo "Crédito ID: {$credito->id}\n";
echo 'Monto Principal: $' . number_format($credito->monto_principal, 2) . "\n";
echo 'Base Total (Principal + Intereses): $' . number_format($credito->base_total, 2) . "\n";
echo 'Total Recargos: $' . number_format($credito->total_recargos, 2) . "\n";
echo 'Total Descuentos: $' . number_format($credito->total_descuentos, 2) . "\n";
echo 'Monto Total (Base + Recargos - Descuentos): $' . number_format($credito->monto_total, 2) . "\n";
echo 'Monto Pagado: $' . number_format($credito->monto_pagado, 2) . "\n";
echo 'Saldo Pendiente: $' . number_format($credito->saldo_pendiente, 2) . "\n";
echo "Estado: {$credito->estado}\n";

echo "\n=== DETALLE DE DESCUENTOS ===\n";
foreach ($credito->descuentos as $descuento) {
    echo "- {$descuento->concepto}: \$" . number_format($descuento->monto, 2) . " ({$descuento->fecha_aplicacion})\n";
}

echo "\n=== DETALLE DE RECARGOS ===\n";
foreach ($credito->recargos as $recargo) {
    echo "- {$recargo->concepto}: \$" . number_format($recargo->monto, 2) . " ({$recargo->fecha_aplicacion})\n";
}

echo "\n=== CÁLCULO MANUAL ===\n";
$base_total = $credito->base_total;
$total_recargos = $credito->total_recargos;
$total_descuentos = $credito->total_descuentos;
$monto_total_calculado = $base_total + $total_recargos - $total_descuentos;
$saldo_calculado = $monto_total_calculado - $credito->monto_pagado;

echo 'Base Total: $' . number_format($base_total, 2) . "\n";
echo 'Total Recargos: $' . number_format($total_recargos, 2) . "\n";
echo 'Total Descuentos: $' . number_format($total_descuentos, 2) . "\n";
echo 'Monto Total Calculado: $' . number_format($monto_total_calculado, 2) . "\n";
echo 'Monto Pagado: $' . number_format($credito->monto_pagado, 2) . "\n";
echo 'Saldo Calculado: $' . number_format($saldo_calculado, 2) . "\n";

echo "\n=== COMPARACIÓN ===\n";
echo 'Monto Total (Modelo): $' . number_format($credito->monto_total, 2) . "\n";
echo 'Monto Total (Calculado): $' . number_format($monto_total_calculado, 2) . "\n";
echo 'Saldo Pendiente (Modelo): $' . number_format($credito->saldo_pendiente, 2) . "\n";
echo 'Saldo Pendiente (Calculado): $' . number_format($saldo_calculado, 2) . "\n";

$monto_total_ok = abs($credito->monto_total - $monto_total_calculado) < 0.01;
$saldo_ok = abs($credito->saldo_pendiente - $saldo_calculado) < 0.01;

echo "\n=== RESULTADO ===\n";
echo 'Monto Total: ' . ($monto_total_ok ? '✅ CORRECTO' : '❌ INCORRECTO') . "\n";
echo 'Saldo Pendiente: ' . ($saldo_ok ? '✅ CORRECTO' : '❌ INCORRECTO') . "\n";

if ($monto_total_ok && $saldo_ok) {
    echo "\n🎉 ¡Los descuentos se están aplicando correctamente!\n";
} else {
    echo "\n❌ Hay un problema con el cálculo de descuentos.\n";
}
