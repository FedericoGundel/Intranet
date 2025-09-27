<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticuloFacturaTable extends Migration
{
    public function up()
    {
        Schema::create('articulo_factura', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('factura_id');
            $table->unsignedBigInteger('id_articulo'); // FK a articulos
            $table->string('nombre');
            $table->integer('cantidad');
            $table->decimal('precio', 15, 2);
            $table->decimal('impuesto', 5, 2)->default(0);
            $table->text('descripcion')->nullable();
            $table->decimal('total', 15, 2);
            $table->timestamps();

            // Claves foráneas
            $table->foreign('factura_id')->references('id')->on('facturas')->onDelete('cascade');
            $table->foreign('id_articulo')->references('id')->on('articulos')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('articulo_factura');
    }
}
