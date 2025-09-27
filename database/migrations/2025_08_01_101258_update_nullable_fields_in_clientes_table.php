<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNullableFieldsInClientesTable extends Migration
{
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('apellido1')->nullable()->change();
            $table->string('apellido2')->nullable()->change();
            $table->string('direccion')->nullable()->change();
            $table->string('codigo_postal')->nullable()->change();
            $table->string('localidad')->nullable()->change();
            $table->string('provincia')->nullable()->change();
            $table->string('pais')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('telefono')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('apellido1')->nullable(false)->change();
            $table->string('apellido2')->nullable(false)->change();
            $table->string('direccion')->nullable(false)->change();
            $table->string('codigo_postal')->nullable(false)->change();
            $table->string('localidad')->nullable(false)->change();
            $table->string('provincia')->nullable(false)->change();
            $table->string('pais')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('telefono')->nullable(false)->change();
        });
    }
}

