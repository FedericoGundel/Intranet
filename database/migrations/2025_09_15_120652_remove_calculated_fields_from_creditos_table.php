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
            $table->dropColumn(['estado', 'monto_pagado', 'saldo_pendiente']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creditos', function (Blueprint $table) {
            $table->enum('estado', ['activo', 'pagado', 'vencido', 'cancelado'])->default('activo');
            $table->decimal('monto_pagado', 12, 2)->default(0.0);
            $table->decimal('saldo_pendiente', 12, 2);
        });
    }
};
