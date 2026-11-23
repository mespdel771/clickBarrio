<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * Clase de pruebas para comprobar que la verificación del correo electrónico funciona correctamente.
 */
class EmailVerificationTest extends TestCase
{
    // Reinicia la base de datos en cada test para evitar conflictos entre pruebas
    use RefreshDatabase;

    /**
     * Comprueba que la pantalla de aviso para verificar el email carga bien.
     *
     * @return void
     */
    public function test_email_verification_screen_can_be_rendered(): void
    {
        // Creamos un usuario que todavía tiene el correo sin verificar
        $user = User::factory()->unverified()->create();

        // Entramos como ese usuario a la ruta de verificación
        $response = $this->actingAs($user)->get('/verify-email');

        // Verificamos que la página carga sin problemas (código 200)
        $response->assertStatus(200);
    }

    /**
     * Prueba que un usuario puede verificar su email usando un enlace válido.
     *
     * @return void
     */
    public function test_email_can_be_verified(): void
    {
        // Creamos un usuario sin verificar
        $user = User::factory()->unverified()->create();

        // Fingimos los eventos para comprobar luego si se lanza el de "verificado" sin ejecutar acciones extra
        Event::fake();

        // Generamos un enlace temporal firmado (como el que llegaría al correo) válido por 60 minutos
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // El usuario hace clic (petición GET) en el enlace de verificación
        $response = $this->actingAs($user)->get($verificationUrl);

        // Comprobamos que se disparó el evento de verificación
        Event::assertDispatched(Verified::class);
        // Recargamos el usuario de la BD (fresh) y miramos que su correo ya consta como verificado
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        // Verificamos que le redirige al dashboard con el parámetro verified=1
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    /**
     * Comprueba que el correo no se verifica si el enlace tiene un código (hash) incorrecto.
     *
     * @return void
     */
    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        // Creamos un usuario sin verificar
        $user = User::factory()->unverified()->create();

        // Creamos un enlace usando un hash que no corresponde con el email real del usuario
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        // Intentamos entrar al enlace manipulado
        $this->actingAs($user)->get($verificationUrl);

        // Comprobamos que en la base de datos sigue apareciendo como NO verificado
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
