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
        Schema::create('gastos_leyma', function (Blueprint $table) {
            $table->id();
            $table->enum('categoria', ['mauro', 'gota', 'inversion']);
            $table->decimal('monto', 12, 2);
            $table->string('concepto');
            $table->date('fecha');
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos_leyma');
    }
};
