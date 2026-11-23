<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Clase de pruebas para comprobar todo el proceso de recuperación y cambio de contraseña olvidada.
 */
class PasswordResetTest extends TestCase
{
    // Limpia la base de datos antes de ejecutar cada prueba
    use RefreshDatabase;

    /**
     * Comprueba que la pantalla de "¿Olvidaste tu contraseña?" carga bien.
     *
     * @return void
     */
    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        // Entramos a la ruta donde se pide el enlace de recuperación
        $response = $this->get('/forgot-password');

        // Verificamos que la vista carga correctamente (código 200)
        $response->assertStatus(200);
    }

    /**
     * Prueba que al introducir un correo registrado se envía la notificación de recuperación.
     *
     * @return void
     */
    public function test_reset_password_link_can_be_requested(): void
    {
        // Interceptamos las notificaciones para no mandar correos reales durante el test
        Notification::fake();

        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Enviamos el formulario pidiendo recuperar la contraseña con su email
        $this->post('/forgot-password', ['email' => $user->email]);

        // Comprobamos que al usuario se le ha enviado la notificación de ResetPassword
        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * Comprueba que el formulario para escribir la nueva contraseña abre bien usando el token del correo.
     *
     * @return void
     */
    public function test_reset_password_screen_can_be_rendered(): void
    {
        // Interceptamos las notificaciones
        Notification::fake();

        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Solicitamos el correo de recuperación
        $this->post('/forgot-password', ['email' => $user->email]);

        // Capturamos la notificación enviada para sacar su token y probar la URL
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            // Entramos a la ruta de cambio de contraseña con el token generado
            $response = $this->get('/reset-password/'.$notification->token);

            // Verificamos que la pantalla carga sin fallos
            $response->assertStatus(200);

            return true;
        });
    }

    /**
     * Prueba que el usuario puede cambiar su contraseña finalmente si tiene un token válido.
     *
     * @return void
     */
    public function test_password_can_be_reset_with_valid_token(): void
    {
        // Interceptamos las notificaciones
        Notification::fake();

        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Pedimos el enlace de recuperación
        $this->post('/forgot-password', ['email' => $user->email]);

        // Usamos el token de la notificación para enviar el formulario con la nueva contraseña
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            // Comprobamos que no hay errores de validación y que nos manda de vuelta al login
            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
