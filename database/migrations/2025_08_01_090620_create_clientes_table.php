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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
        $table->string('nombre');
         $table->string('apellido1')->nullable();
        $table->string('apellido2')->nullable();
        $table->enum('tipo_cliente', ['empresa', 'autonomo', 'particular']);
        $table->string('nif')->unique(); // DNI, CUIT, CIF, etc.
        $table->string('direccion');
        $table->string('codigo_postal');
        $table->string('localidad');
        $table->string('provincia');
        $table->string('pais');
        $table->string('email')->nullable();
        $table->string('telefono')->nullable();
        $table->timestamps();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
