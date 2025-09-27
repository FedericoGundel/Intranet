<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('creditos', function (Blueprint $table) {
            // Agregar cantidad_cuotas
            $table->integer('cantidad_cuotas')->after('dias_credito');

            // Remover fecha_vencimiento
            $table->dropColumn('fecha_vencimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creditos', function (Blueprint $table) {
            // Restaurar fecha_vencimiento
            $table->date('fecha_vencimiento');

            // Remover cantidad_cuotas
            $table->dropColumn('cantidad_cuotas');
        });
    }
};
