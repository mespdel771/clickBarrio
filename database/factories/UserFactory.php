<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Fábrica encargada de generar instancias de prueba para el modelo User.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Contraseña actual cifrada utilizada por defecto en la fábrica.
     *
     * @var string|null
     */
    protected static ?string $password;

    /**
     * Define el estado por defecto del modelo User con datos ficticios.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * Indica que la dirección de correo electrónico del usuario debe quedar sin verificar.
     *
     * @return static
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
