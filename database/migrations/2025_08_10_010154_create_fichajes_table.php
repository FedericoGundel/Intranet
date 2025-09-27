<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFichajesTable extends Migration
{
    public function up()
    {
        Schema::create('fichajes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->timestamp('fecha_entrada')->nullable();
            $table->timestamp('fecha_salida')->nullable();

            $table->timestamp('ultima_entrada')->nullable();
            $table->timestamp('ultima_salida')->nullable();

            $table->integer('tiempo_descanso')->default(0); // minutos

            $table->string('localizacion')->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fichajes');
    }
}
