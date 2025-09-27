<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carga_articulo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_articulo');
            $table->integer('cantidad');
            $table->date('fecha');
            $table->timestamps();

            // Si querés relacionar con la tabla articulos
            $table->foreign('id_articulo')
                ->references('id')->on('articulos')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carga_articulo');
    }
};
