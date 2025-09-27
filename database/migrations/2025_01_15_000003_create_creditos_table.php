<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes_credito')->onDelete('cascade');
            $table->string('numero_credito')->unique();
            $table->decimal('monto_principal', 12, 2);
            $table->decimal('monto_total', 12, 2);
            
            // Configuración del crédito
            $table->string('tipo_pago'); // diario, semanal, quincenal, contado
            $table->decimal('porcentaje_base', 5, 2); // Porcentaje base del tipo
            $table->decimal('porcentaje_final', 5, 2); // Porcentaje calculado con días
            $table->integer('dias_credito'); // Días exactos del crédito
            
            // Sistema de mínimos
            $table->decimal('monto_minimo', 10, 2)->nullable(); // Mínimo personalizado
            $table->boolean('usar_minimo')->default(false); // Usar mínimo o porcentaje
            
            // Fechas
            $table->date('fecha_inicio');
            $table->date('fecha_vencimiento');
            
            // Estado y control
            $table->enum('estado', ['activo', 'pagado', 'vencido', 'cancelado'])->default('activo');
            $table->decimal('monto_pagado', 12, 2)->default(0.00);
            $table->decimal('saldo_pendiente', 12, 2);
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditos');
    }
};
