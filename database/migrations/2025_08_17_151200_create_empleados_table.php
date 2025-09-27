<?php
// database/migrations/2025_08_17_000000_create_empleados_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('apellido', 120)->nullable();
            $table->string('dni', 20)->unique()->nullable();
            $table->string('email', 150)->unique()->nullable();
            $table->string('telefono', 50)->nullable();


            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};