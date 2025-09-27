<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticuloFactory extends Factory
{
    public function definition()
    {
        return [
            'nombre' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(),
            'codigo' => strtoupper($this->faker->bothify('???-#####')),
            'precio' => $this->faker->randomFloat(2, 100, 10000),
            'imagen' => null, // Factory no sube imagen, lo dejás null
        ];
    }
}
