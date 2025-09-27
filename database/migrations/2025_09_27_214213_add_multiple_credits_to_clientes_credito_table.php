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
        Schema::table('clientes_credito', function (Blueprint $table) {
            $table->boolean('permite_creditos_multiples')->default(true)->after('observaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes_credito', function (Blueprint $table) {
            $table->dropColumn('permite_creditos_multiples');
        });
    }
};
