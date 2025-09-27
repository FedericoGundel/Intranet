<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // (Opcional) Normalizar valores "0000-00-00" a NULL antes de cambiar el schema (MySQL modo estricto)
        try {
            DB::statement("UPDATE facturas SET fecha_vencimiento = NULL WHERE fecha_vencimiento = '0000-00-00'");
        } catch (\Throwable $e) {
            // si no aplica (otro motor o tipo), seguimos
        }

        Schema::table('facturas', function (Blueprint $table) {
            // Asegurate de tener instalado doctrine/dbal para usar change()
            // composer require doctrine/dbal
            $table->date('fecha_vencimiento')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Antes de volver a NOT NULL, asegurate de que no haya NULLs
        try {
            DB::statement("UPDATE facturas SET fecha_vencimiento = CURRENT_DATE WHERE fecha_vencimiento IS NULL");
        } catch (\Throwable $e) {
            // fallback silencioso
        }

        Schema::table('facturas', function (Blueprint $table) {
            $table->date('fecha_vencimiento')->nullable(false)->change();
        });
    }
};
