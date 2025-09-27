<?php
// database/migrations/2025_08_17_000100_create_vendedores_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendedores', function (Blueprint $table) {
            // empleado_id es a la vez PK y FK
            $table->unsignedBigInteger('empleado_id')->primary();
            // campos propios del rol vendedor:
            $table->decimal('meta_mensual', 12, 2)->nullable();
            $table->decimal('comision_porcentaje', 5, 2)->nullable(); // ej. 3.50 = 3.5%

            $table->timestamps();

            $table->foreign('empleado_id')
                ->references('id')->on('empleados')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('vendedores');
    }
};