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
        Schema::create('configuracion_creditos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_credito'); // diario, semanal, quincenal, contado
            $table->decimal('porcentaje_base', 5, 2); // 10.00, 10.00, 2.50, 15.00
            $table->integer('dias_minimos'); // 14, 14, 28, 0
            $table->decimal('monto_minimo_base', 10, 2)->default(4000.00);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar configuraciones por defecto
        DB::table('configuracion_creditos')->insert([
            [
                'tipo_credito' => 'diario',
                'porcentaje_base' => 10.00,
                'dias_minimos' => 14,
                'monto_minimo_base' => 4000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_credito' => 'semanal',
                'porcentaje_base' => 10.00,
                'dias_minimos' => 14,
                'monto_minimo_base' => 4000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_credito' => 'quincenal',
                'porcentaje_base' => 2.50,
                'dias_minimos' => 28,
                'monto_minimo_base' => 4000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo_credito' => 'contado',
                'porcentaje_base' => 15.00,
                'dias_minimos' => 0,
                'monto_minimo_base' => 4000.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_creditos');
    }
};
