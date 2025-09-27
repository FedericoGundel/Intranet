<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PagoService
{
    public function registrarPago(array $data): Pago
    {
        return DB::transaction(function () use ($data) {
            /** @var \App\Models\Factura $factura */
            $factura = Factura::lockForUpdate()->findOrFail($data['factura_id']);

            // Normalizar monto
            $monto = $this->toDecimal($data['monto'] ?? 0);

            if ($monto <= 0) {
                throw ValidationException::withMessages(['monto' => 'El monto debe ser mayor a cero.']);
            }

            // 🔴 TOTAL REAL DE LA FACTURA desde las líneas (ya con desc+imp)
            $totalFacturado = (float) $factura->articuloFacturas()->sum('total');

            // Pagos no anulados (SoftDeletes excluye por defecto)
            $pagadoActual = (float) $factura->pagos()->sum('monto');

            // Saldo
            $saldoActual = max(0, $totalFacturado - $pagadoActual);

            // Evitar falsos positivos por redondeo
            if (round($monto, 2) - round($saldoActual, 2) > 0.00001) {
                throw ValidationException::withMessages([
                    'monto' => 'El monto aplicado excede el saldo de la factura.'
                ]);
            }

            // Crear pago
            $pago = Pago::create([
                'factura_id' => $factura->id,
                'numero' => $this->siguienteNumeroRecibo(),
                'fecha' => $data['fecha'],
                'monto' => round($monto, 2),
                'metodo' => $data['metodo'] ?? null,
                'referencia' => $data['referencia'] ?? null,
                'notas' => $data['notas'] ?? null,
                'cobrador_id' => $data['cobrador_id'] ?? null,  // 👈 agregado
            ]);

            return $pago;
        });
    }

    public function actualizarPago(int $pagoId, array $data): Pago
    {
        return DB::transaction(function () use ($pagoId, $data) {
            /** @var \App\Models\Pago $pago */
            $pago = Pago::lockForUpdate()->findOrFail($pagoId);

            // Si querés permitir mover el pago a otra factura:
            $targetFacturaId = $data['factura_id'] ?? $pago->factura_id;

            /** @var \App\Models\Factura $factura */
            $factura = Factura::lockForUpdate()->findOrFail($targetFacturaId);

            // Normalizar monto
            $monto = round($this->toDecimal($data['monto'] ?? 0), 2);

            if ($monto <= 0) {
                throw ValidationException::withMessages(['monto' => 'El monto debe ser mayor a cero.']);
            }

            // Total real de la factura desde líneas
            $totalFacturado = (float) $factura->articuloFacturas()->sum('total');

            // Suma de pagos vigentes EXCLUYENDO este pago
            $pagadoOtros = (float) $factura
                ->pagos()
                ->where('id', '!=', $pago->id)
                ->sum('monto');

            // Saldo disponible
            $saldoActual = max(0, $totalFacturado - $pagadoOtros);

            // Validar contra saldo
            if (round($monto, 2) - round($saldoActual, 2) > 0.00001) {
                throw ValidationException::withMessages([
                    'monto' => 'El monto aplicado excede el saldo de la factura.'
                ]);
            }

            // Actualizar
            $pago->update([
                'factura_id' => $targetFacturaId,
                'fecha' => $data['fecha'],
                'monto' => $monto,
                'metodo' => $data['metodo'] ?? null,
                'referencia' => $data['referencia'] ?? null,
                'notas' => $data['notas'] ?? null,
                'cobrador_id' => empty($data['cobrador_id']) ? null : $data['cobrador_id'],
            ]);

            return $pago->fresh();
        });
    }

    // 👇 Helper para normalizar "1.234,56" => 1234.56, "0,50" => 0.5, etc.
    private function toDecimal($value): float
    {
        $v = trim((string) $value);

        // sacar espacios normales y NBSP (al pegar desde Excel a veces vienen)
        $v = str_replace(["\u{00A0}", ' '], '', $v);

        $hasComma = str_contains($v, ',');
        $hasDot = str_contains($v, '.');

        if ($hasComma && $hasDot) {
            // Si la última coma está a la derecha del último punto => formato EU "1.234,56"
            if (strrpos($v, ',') > strrpos($v, '.')) {
                $v = str_replace('.', '', $v);  // borro miles
                $v = str_replace(',', '.', $v);  // coma -> punto
            } else {
                // Formato US "1,234.56"
                $v = str_replace(',', '', $v);  // borro miles
            }
        } elseif ($hasComma) {
            // Solo coma => trato como decimal
            $v = str_replace(',', '.', $v);
        }
        // dejo solo dígitos, punto y signo
        $v = preg_replace('/[^0-9\.\-]/', '', $v);

        // si quedó vacío, es 0
        if ($v === '' || $v === '-' || $v === '.')
            return 0.0;

        return (float) $v;
    }

    private function siguienteNumeroRecibo(): string
    {
        // Simple consecutivo basado en ID (incluye borrados con soft delete para no repetir)
        $next = (int) (optional(Pago::withTrashed()->orderByDesc('id')->first())->id ?? 0) + 1;
        return 'R' . str_pad((string) $next, 8, '0', STR_PAD_LEFT);  // R00000001, R00000002, ...
    }

    public function eliminarPago(Pago $pago, bool $definitivo = false): void
    {
        DB::transaction(function () use ($pago, $definitivo) {
            if ($definitivo) {
                $pago->forceDelete();
            } else {
                // si ya está anulado, no hacemos nada para que sea idempotente
                if (is_null($pago->deleted_at)) {
                    $pago->delete();
                }
            }
        });
    }

    /**
     * Helper por ID para usar desde el controller.
     */
    public function eliminarPagoPorId(int $id, bool $definitivo = false): void
    {
        $pago = Pago::withTrashed()->findOrFail($id);
        $this->eliminarPago($pago, $definitivo);
    }

    /**
     * (Opcional) Restaurar un pago anulado (soft-deleted).
     */
    public function restaurarPagoPorId(int $id): void
    {
        DB::transaction(function () use ($id) {
            $pago = Pago::onlyTrashed()->findOrFail($id);
            $pago->restore();
        });
    }
}
