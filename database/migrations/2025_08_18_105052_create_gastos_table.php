<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();

            $table->date('fecha');
            $table->string('categoria', 50);        // ej: nomina, nafta, viajes, camion_muebles, camion_fletes, empleados, otros
            $table->string('subcategoria', 50)->nullable();

            $table->decimal('monto', 12, 2);        // sin moneda
            $table->string('metodo_pago', 100)->nullable();
            $table->text('descripcion')->nullable();

            $table->unsignedBigInteger('empleado_id')->nullable();     // opcional (viáticos, o para gastos de nómina por empleado)
            $table->unsignedBigInteger('nomina_pago_id')->nullable();  // vínculo directo al pago de nómina
            $table->string('comprobante_path')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['fecha', 'categoria']);
            $table->index('empleado_id');

            // Un gasto por pago de nómina (evita duplicados)
            $table->unique('nomina_pago_id');

            // FKs
            $table->foreign('empleado_id')->references('id')->on('empleados')->nullOnDelete();
            $table->foreign('nomina_pago_id')->references('id')->on('pago_nominas')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
