<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('articulo_factura', function (Blueprint $table) {
            $table->decimal('descuento', 5, 2)->default(0)->after('precio'); 
            // 5,2 = admite hasta 999.99 %, pero nosotros usaremos hasta 100 %
        });
    }

    public function down(): void
    {
        Schema::table('articulo_factura', function (Blueprint $table) {
            $table->dropColumn('descuento');
        });
    }
};
