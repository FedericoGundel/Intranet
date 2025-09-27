<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('nomina_detalles', function (Blueprint $table) {
            // Si antes era enum, lo cambiamos a string
            $table->string('tipo', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('nomina_detalles', function (Blueprint $table) {
            // Volver atrás (si era enum originalmente)
            $table->enum('tipo', ['factura', 'pago'])->change();
        });
    }
};
