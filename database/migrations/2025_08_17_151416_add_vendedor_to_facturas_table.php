<?php
// database/migrations/2025_08_17_000200_add_vendedor_to_facturas_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->unsignedBigInteger('vendedor_id')->nullable()->after('id'); // o donde quieras
            // MUY IMPORTANTE: referencia a vendedores.empleado_id (no a empleados.id)
            $table->foreign('vendedor_id')
                ->references('empleado_id')->on('vendedores')
                ->cascadeOnUpdate()
                ->nullOnDelete(); // si borrás el vendedor, la factura queda sin vendedor
        });
    }
    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropForeign(['vendedor_id']);
            $table->dropColumn('vendedor_id');
        });
    }
};