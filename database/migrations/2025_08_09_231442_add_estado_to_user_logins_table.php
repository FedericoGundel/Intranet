<?php

// En el archivo de migración generado
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToUserLoginsTable extends Migration
{
    public function up()
    {
        Schema::table('user_logins', function (Blueprint $table) {
            // Agregar el campo 'estado' (booleano)
            $table->boolean('estado')->default(true);  // 'true' por defecto, puedes cambiarlo si lo deseas
        });
    }

    public function down()
    {
        Schema::table('user_logins', function (Blueprint $table) {
            // Eliminar el campo 'estado'
            $table->dropColumn('estado');
        });
    }
}
