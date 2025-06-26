<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@reservas.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Secretaria
        $secretaria = User::create([
            'name' => 'María Secretaria',
            'email' => 'secretaria@reservas.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $secretaria->assignRole('secretaria');

        // Cliente
        $cliente = User::create([
            'name' => 'Juan Cliente',
            'email' => 'cliente@reservas.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $cliente->assignRole('cliente');

        // Usuarios adicionales para pruebas
        User::factory(5)->create()->each(function ($user) {
            $user->assignRole('cliente');
        });

        User::factory(2)->create()->each(function ($user) {
            $user->assignRole('secretaria');
        });
    }
}