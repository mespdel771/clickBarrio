<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clase de pruebas para comprobar que la confirmación de contraseña funciona bien antes de hacer acciones delicadas.
 */
class PasswordConfirmationTest extends TestCase
{
    // Reinicia la base de datos en cada prueba para que esté limpia
    use RefreshDatabase;

    /**
     * Comprueba que la pantalla para confirmar la contraseña carga correctamente.
     *
     * @return void
     */
    public function test_confirm_password_screen_can_be_rendered(): void
    {
        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Entramos con el usuario logueado a la ruta de confirmar contraseña
        $response = $this->actingAs($user)->get('/confirm-password');

        // Verificamos que la página responde sin fallos (código 200)
        $response->assertStatus(200);
    }

    /**
     * Prueba que el usuario puede confirmar su identidad escribiendo su contraseña correcta.
     *
     * @return void
     */
    public function test_password_can_be_confirmed(): void
    {
        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Enviamos el formulario poniendo la contraseña correcta
        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ]);

        // Comprobamos que le redirige a donde iba y que no hay errores en la sesión
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    /**
     * Comprueba que da error si el usuario mete una contraseña que no es la suya.
     *
     * @return void
     */
    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Intentamos confirmar usando una contraseña equivocada
        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        // Verificamos que el sistema devuelve errores de validación en la sesión
        $response->assertSessionHasErrors();
    }
}
