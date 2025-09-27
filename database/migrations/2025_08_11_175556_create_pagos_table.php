<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_pagos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->cascadeOnDelete();
            $table->string('numero')->unique();   // nro de recibo
            $table->date('fecha');
            $table->decimal('monto', 14, 2);      // misma moneda que la factura
            $table->string('metodo')->nullable(); // efectivo, transf, tarjeta, MP...
            $table->string('referencia')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['factura_id', 'fecha']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('pagos');
    }
};
