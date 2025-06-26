<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => '+569 ' . $this->faker->numerify('#### ####'),
            'documento' => $this->faker->unique()->numerify('########-#'),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'direccion' => $this->faker->address(),
            'activo' => $this->faker->boolean(90), // 90% probabilidad de estar activo
        ];
    }
}