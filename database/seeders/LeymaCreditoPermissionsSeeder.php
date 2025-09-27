<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LeymaCreditoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos para Leyma Créditos (un permiso por vista)
        $permissions = [
            'leyma_creditos',           // Dashboard principal
            'leyma_clientes',           // Gestión de clientes
            'leyma_creditos_gestion',   // Gestión de créditos
            'leyma_gastos',             // Gestión de gastos
            'leyma_inyecciones',        // Gestión de inyecciones
            'leyma_prestamos_familiares' // Gestión de préstamos familiares
        ];

        // Crear permisos
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar permisos al rol de administrador (si existe)
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // Crear rol específico para Leyma Créditos
        $leymaRole = Role::firstOrCreate(['name' => 'leyma-creditos']);
        $leymaRole->givePermissionTo($permissions);

        // Crear rol de operador con permisos limitados
        $operadorRole = Role::firstOrCreate(['name' => 'leyma-operador']);
        $operadorPermissions = [
            'leyma_creditos',           // Dashboard
            'leyma_clientes',           // Clientes
            'leyma_creditos_gestion',   // Créditos
            'leyma_gastos'              // Gastos (sin inyecciones ni préstamos familiares)
        ];
        $operadorRole->givePermissionTo($operadorPermissions);

        $this->command->info('Permisos de Leyma Créditos creados exitosamente');
    }
}
