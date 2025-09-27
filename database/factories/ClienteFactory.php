<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName,
            'apellido1' => $this->faker->lastName,
            'apellido2' => $this->faker->lastName,
            'tipo_cliente' => $this->faker->randomElement(['particular', 'empresa']),
            'nif' => strtoupper($this->faker->unique()->bothify('########?')),
            'direccion' => $this->faker->streetAddress,
            'codigo_postal' => $this->faker->postcode,
            'localidad' => $this->faker->city,
            'provincia' => $this->faker->state,
            'pais' => $this->faker->country,
            'email' => $this->faker->unique()->safeEmail,
            'telefono' => $this->faker->phoneNumber,
        ];
    }
}
