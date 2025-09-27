<?php

// database/migrations/2025_08_21_000200_add_cobrador_id_to_pagos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->unsignedBigInteger('cobrador_id')->nullable();
            $table
                ->foreign('cobrador_id')
                ->references('empleado_id')
                ->on('cobradores')
                ->onDelete('set null')
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['cobrador_id']);
            $table->dropColumn('cobrador_id');
        });
    }
};
