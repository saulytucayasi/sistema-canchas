<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Carlos',
                'apellido' => 'González',
                'email' => 'carlos.gonzalez@email.com',
                'telefono' => '+569 8765 4321',
                'documento' => '12345678-9',
                'fecha_nacimiento' => '1985-03-15',
                'direccion' => 'Av. Providencia 1234, Santiago',
                'activo' => true,
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'email' => 'ana.martinez@email.com',
                'telefono' => '+569 9876 5432',
                'documento' => '98765432-1',
                'fecha_nacimiento' => '1990-07-22',
                'direccion' => 'Calle Los Leones 567, Las Condes',
                'activo' => true,
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Rodríguez',
                'email' => 'pedro.rodriguez@email.com',
                'telefono' => '+569 5555 1234',
                'documento' => '11223344-5',
                'fecha_nacimiento' => '1988-12-05',
                'direccion' => 'Pasaje San Martín 890, Ñuñoa',
                'activo' => true,
            ],
            [
                'nombre' => 'Sofía',
                'apellido' => 'López',
                'email' => 'sofia.lopez@email.com',
                'telefono' => '+569 7777 9999',
                'documento' => '55667788-9',
                'fecha_nacimiento' => '1992-04-18',
                'direccion' => 'Av. Las Condes 2000, Las Condes',
                'activo' => true,
            ],
            [
                'nombre' => 'Miguel',
                'apellido' => 'Fernández',
                'email' => 'miguel.fernandez@email.com',
                'telefono' => '+569 3333 7777',
                'documento' => '99887766-5',
                'fecha_nacimiento' => '1983-09-30',
                'direccion' => 'Calle Brasil 456, Valparaíso',
                'activo' => true,
            ]
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }

        // Crear clientes adicionales con factory
        Cliente::factory(15)->create();
    }
}