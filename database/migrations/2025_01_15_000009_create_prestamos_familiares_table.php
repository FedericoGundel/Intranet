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
        Schema::create('prestamos_familiares', function (Blueprint $table) {
            $table->id();
            $table->string('familiar_nombre');
            $table->string('familiar_dni')->nullable();
            $table->string('familiar_telefono')->nullable();
            $table->decimal('monto', 12, 2);
            $table->date('fecha_prestamo');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['activo', 'pagado', 'vencido'])->default('activo');
            $table->decimal('monto_pagado', 12, 2)->default(0.00);
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
        Schema::dropIfExists('prestamos_familiares');
    }
};
