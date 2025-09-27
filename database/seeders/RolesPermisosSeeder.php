<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        // Eliminar todos los permisos y roles existentes
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2) Deshabilitar FKs y truncar en orden (MySQL/MariaDB)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // Lista de permisos manuales (uno por cada vista que quieras)
        $permisos = [
            'estadisticas',
            'usuarios',
            'clientes',
            'email_marketing',
            'articulos',
            'crear_factura',
            'facturas',
            'cobros',
            'fichaje',
            'nominas',
            'gastos',
            // Permisos del sistema de créditos
            'creditos',
            'creditos_crear',
            'creditos_editar',
            'creditos_eliminar',
            'creditos_pagos',
            'creditos_reportes',
            'clientes_credito',
            'garantes'
        ];

        // Crear permisos
        foreach ($permisos as $p) {
            Permission::create(['name' => $p, 'guard_name' => 'web']);
        }

        /*
         * $admin  = Role::create(['name' => 'admin']);
         * $ventas = Role::create(['name' => 'ventas']);
         * $tecnico = Role::create(['name' => 'tecnico']);
         *
         *
         * $admin->givePermissionTo($permisos);
         * $ventas->givePermissionTo(['ver.dashboard', 'ver.ventas']);
         * $tecnico->givePermissionTo(['ver.dashboard', 'ver.tecnica']);
         *
         * // Asignar rol de admin al usuario con ID 1
         * $user = User::find(1);
         * if ($user) {
         *     $user->assignRole('admin');
         * }
         */

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
