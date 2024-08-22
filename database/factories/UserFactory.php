<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            "nombre_completo" => $this->faker->name,
            "email" => $this->faker->unique->email,
            "contrasenia" => Hash::make("1234567890"),
        ];
    }
}
