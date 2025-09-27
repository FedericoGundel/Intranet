<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nominas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('empleado_id')->constrained()->cascadeOnDelete();
            $t->string('periodo', 10)->nullable(); // ej: 2025-W33 (opcional)
            $t->date('desde'); // inicio de semana (incl.)
            $t->date('hasta'); // fin de semana (incl.)
            $t->decimal('porcentaje_comision', 5, 2)->default(9.00);
            $t->decimal('total_base', 14, 2)->default(0);
            $t->decimal('total_comision', 14, 2)->default(0);
            $t->decimal('ajustes', 14, 2)->default(0);
            $t->decimal('total_calculado', 14, 2)->default(0);
            $t->decimal('total_pagado', 14, 2)->default(0);
            $t->decimal('saldo', 14, 2)->default(0);
            $t->enum('estado', ['borrador', 'cerrada', 'parcial', 'pagada'])->default('borrador');
            $t->timestamp('cerrada_at')->nullable();
            $t->timestamp('pagada_at')->nullable();
            $t->json('meta')->nullable();
            $t->timestamps();

            $t->unique(['empleado_id', 'desde', 'hasta']); // una nómina por empleado+rango
        });

        Schema::create('nomina_detalles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('nomina_id')->constrained('nominas')->cascadeOnDelete();
            $t->unsignedBigInteger('factura_id')->nullable(); // null si es ajuste manual
            $t->enum('tipo', ['factura', 'ajuste'])->default('factura');
            $t->string('descripcion')->nullable(); // para ajustes
            $t->decimal('monto_base', 14, 2)->default(0);     // total de factura o importe del ajuste (+/-)
            $t->decimal('porcentaje', 5, 2)->nullable();      // % aplicado a esa línea (factura)
            $t->decimal('comision_calculada', 14, 2)->default(0);
            $t->timestamps();

            $t->index('factura_id');
        });

        Schema::create('pago_nominas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('nomina_id')->constrained('nominas')->cascadeOnDelete();
            $t->date('fecha');
            $t->decimal('importe', 14, 2);
            $t->string('metodo')->nullable(); // Efectivo, Transferencia, etc.
            $t->string('nota')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_nominas');
        Schema::dropIfExists('nomina_detalles');
        Schema::dropIfExists('nominas');
    }
};