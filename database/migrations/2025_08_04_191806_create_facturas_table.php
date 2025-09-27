<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturasTable extends Migration
{
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->string('numero')->unique();
            $table->date('fecha');
            $table->date('fecha_vencimiento')->nullable();
            $table->text('condiciones')->nullable();

            $table->string('nombre');
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->enum('tipo_cliente', ['empresa', 'autonomo', 'particular']);
            $table->string('nif');
            $table->string('codigo_postal')->nullable();
            $table->string('pais')->nullable();
            $table->string('provincia')->nullable();
            $table->string('localidad')->nullable();
            $table->boolean('eliminar_imagen')->default(false);

            $table->timestamps();

            $table->foreign('id_cliente')->references('id')->on('clientes')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('facturas');
    }
}
