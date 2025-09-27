<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class AsignarPermisosCreditos extends Command
{
    protected $signature = 'creditos:asignar-permisos {user_id}';
    protected $description = 'Asigna todos los permisos de créditos a un usuario específico';

    public function handle()
    {
        $userId = $this->argument('user_id');

        $user = User::find($userId);
        if (!$user) {
            $this->error("Usuario con ID {$userId} no encontrado.");
            return 1;
        }

        $permisosCreditos = [
            'creditos',
            'creditos_crear',
            'creditos_editar',
            'creditos_eliminar',
            'creditos_pagos',
            'creditos_reportes',
            'clientes_credito',
            'garantes'
        ];

        // Verificar que todos los permisos existan
        foreach ($permisosCreditos as $permiso) {
            if (!Permission::where('name', $permiso)->exists()) {
                $this->error("El permiso '{$permiso}' no existe.");
                return 1;
            }
        }

        // Asignar permisos al usuario
        $user->givePermissionTo($permisosCreditos);

        $this->info("Permisos de créditos asignados correctamente al usuario: {$user->name} ({$user->email})");

        // Mostrar permisos asignados
        $this->info('Permisos asignados:');
        foreach ($permisosCreditos as $permiso) {
            $this->line("- {$permiso}");
        }

        return 0;
    }
}
