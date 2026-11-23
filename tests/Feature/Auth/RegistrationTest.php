<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clase de pruebas para comprobar que el registro de nuevos usuarios funciona correctamente.
 */
class RegistrationTest extends TestCase
{
    // Limpia y reinicia la base de datos antes de cada prueba para que no haya usuarios duplicados
    use RefreshDatabase;

    /**
     * Comprueba que la página de registro carga sin errores (código 200).
     *
     * @return void
     */
    public function test_registration_screen_can_be_rendered(): void
    {
        // Hacemos una petición GET a la ruta de registro
        $response = $this->get('/register');

        // Verificamos que la vista carga bien
        $response->assertStatus(200);
    }

    /**
     * Prueba que un usuario nuevo se puede registrar rellenando el formulario correctamente.
     *
     * @return void
     */
    public function test_new_users_can_register(): void
    {
        // Enviamos los datos de un usuario nuevo por POST a /register
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Comprobamos que tras registrarse inicia sesión automáticamente y va al dashboard
        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
