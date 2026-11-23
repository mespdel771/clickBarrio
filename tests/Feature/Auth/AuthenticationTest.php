<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clase de pruebas para comprobar que el inicio y cierre de sesión funcionan bien.
 */
class AuthenticationTest extends TestCase
{
    // Limpia y reinicia la base de datos antes de cada test para que no se mezclen datos
    use RefreshDatabase;

    /**
     * Comprueba que la página de login carga correctamente (código 200).
     *
     * @return void
     */
    public function test_login_screen_can_be_rendered(): void
    {
        // Entramos a la ruta /login
        $response = $this->get('/login');

        // Verificamos que la página responde sin errores
        $response->assertStatus(200);
    }

    /**
     * Prueba que un usuario registrado puede iniciar sesión con sus datos correctos.
     *
     * @return void
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        // Creamos un usuario de prueba en la base de datos
        $user = User::factory()->create();

        // Enviamos el formulario de login con el email del usuario y la contraseña por defecto
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Comprobamos que el usuario ha quedado logueado y le redirige al dashboard
        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    /**
     * Comprueba que no deja entrar si la contraseña está mal.
     *
     * @return void
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Intentamos iniciar sesión poniendo una contraseña incorrecta
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        // Verificamos que sigue como invitado (no ha iniciado sesión)
        $this->assertGuest();
    }

    /**
     * Prueba que un usuario que ya ha iniciado sesión puede cerrarla correctamente.
     *
     * @return void
     */
    public function test_users_can_logout(): void
    {
        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Simulamos que ya está logueado y hacemos petición para cerrar sesión
        $response = $this->actingAs($user)->post('/logout');

        // Comprobamos que se ha cerrado la sesión y redirige al inicio
        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
