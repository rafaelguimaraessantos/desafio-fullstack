<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    public function definition()
    {
        return [
            'description' => $this->faker->sentence(3),
            'numberOfClients' => $this->faker->numberBetween(1, 100),
            'gigabytesStorage' => $this->faker->numberBetween(1, 500),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'active' => true,
        ];
    }
}