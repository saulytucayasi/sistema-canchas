<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // Usuarios
            'gestionar_usuarios',
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',
            
            // Clientes
            'gestionar_clientes',
            'ver_clientes',
            'crear_clientes',
            'editar_clientes',
            'eliminar_clientes',
            
            // Canchas
            'gestionar_canchas',
            'ver_canchas',
            'crear_canchas',
            'editar_canchas',
            'eliminar_canchas',
            
            // Reservas
            'gestionar_reservas',
            'ver_reservas',
            'crear_reservas',
            'editar_reservas',
            'cancelar_reservas',
            'ver_propias_reservas',
            'editar_propias_reservas',
            
            // Dashboard
            'ver_dashboard_admin',
            'ver_dashboard_secretaria',
            'ver_dashboard_cliente',
            
            // Reportes
            'ver_reportes',
            'generar_reportes'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $admin = Role::create(['name' => 'admin']);
        $secretaria = Role::create(['name' => 'secretaria']);
        $cliente = Role::create(['name' => 'cliente']);

        // Asignar permisos a Admin (todos los permisos)
        $admin->givePermissionTo(Permission::all());

        // Asignar permisos a Secretaria
        $secretaria->givePermissionTo([
            'gestionar_clientes',
            'ver_clientes',
            'crear_clientes',
            'editar_clientes',
            'eliminar_clientes',
            'gestionar_reservas',
            'ver_reservas',
            'crear_reservas',
            'editar_reservas',
            'cancelar_reservas',
            'ver_canchas',
            'ver_dashboard_secretaria'
        ]);

        // Asignar permisos a Cliente
        $cliente->givePermissionTo([
            'ver_propias_reservas',
            'editar_propias_reservas',
            'crear_reservas',
            'ver_canchas',
            'ver_dashboard_cliente'
        ]);
    }
}