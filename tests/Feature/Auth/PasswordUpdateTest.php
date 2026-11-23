<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Clase de pruebas para comprobar que el usuario puede cambiar su contraseña desde su perfil.
 */
class PasswordUpdateTest extends TestCase
{
    // Reinicia la base de datos antes de cada prueba para que esté limpia
    use RefreshDatabase;

    /**
     * Prueba que la contraseña se actualiza correctamente cuando se pone bien la actual.
     *
     * @return void
     */
    public function test_password_can_be_updated(): void
    {
        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Simulamos que el usuario está en /profile y envía el formulario para cambiar su contraseña
        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        // Comprobamos que no hay errores y que le devuelve a la página de perfil
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        // Recargamos el usuario de la BD y verificamos que el hash de la nueva contraseña coincide
        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    /**
     * Comprueba que no deja cambiar la contraseña si el usuario escribe mal su contraseña actual.
     *
     * @return void
     */
    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Intentamos cambiar la contraseña poniendo una contraseña actual incorrecta
        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        // Verificamos que da error en el campo current_password y redirige de vuelta al perfil
        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }
}
