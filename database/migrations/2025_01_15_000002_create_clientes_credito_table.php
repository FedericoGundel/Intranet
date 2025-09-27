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
        Schema::create('clientes_credito', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('dni')->unique();
            $table->text('domicilio');
            $table->string('comercio_negocio')->nullable();
            $table->string('telefono');
            
            // Datos del garante como campos simples
            $table->string('garante_nombre')->nullable();
            $table->string('garante_dni')->nullable();
            $table->string('garante_telefono')->nullable();
            $table->text('garante_domicilio')->nullable();
            
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes_credito');
    }
};
