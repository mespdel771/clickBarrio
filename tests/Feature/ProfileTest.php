<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clase de pruebas para comprobar que el usuario puede ver, editar y borrar su perfil.
 */
class ProfileTest extends TestCase
{
    // Reinicia la base de datos antes de cada prueba para que esté limpia
    use RefreshDatabase;

    /**
     * Comprueba que la página de perfil carga bien cuando el usuario ha iniciado sesión.
     *
     * @return void
     */
    public function test_profile_page_is_displayed(): void
    {
        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Entramos como ese usuario a la ruta /profile
        $response = $this
            ->actingAs($user)
            ->get('/profile');

        // Verificamos que la página responde sin errores (código 200)
        $response->assertOk();
    }

    /**
     * Prueba que el usuario puede actualizar su nombre y su correo electrónico.
     *
     * @return void
     */
    public function test_profile_information_can_be_updated(): void
    {
        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Enviamos los nuevos datos por PATCH a /profile
        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        // Comprobamos que no hay errores de validación y redirige de vuelta al perfil
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        // Recargamos los datos del usuario desde la base de datos
        $user->refresh();

        // Verificamos que se han guardado los cambios y que el nuevo correo pasa a estar sin verificar
        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    /**
     * Comprueba que si el usuario cambia su nombre pero deja el mismo email, su correo sigue verificado.
     *
     * @return void
     */
    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        // Creamos un usuario de prueba (que por defecto viene con el email verificado)
        $user = User::factory()->create();

        // Actualizamos solo el nombre manteniendo el mismo correo que ya tenía
        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        // Verificamos que guarda sin errores y vuelve a /profile
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        // Comprobamos que la fecha de verificación del correo sigue intacta (no es nula)
        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    /**
     * Prueba que un usuario puede eliminar su propia cuenta poniendo su contraseña correcta.
     *
     * @return void
     */
    public function test_user_can_delete_their_account(): void
    {
        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Enviamos la petición DELETE a /profile confirmando con su contraseña
        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        // Comprobamos que no hay errores y le manda a la página de inicio
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        // Verificamos que se ha cerrado la sesión y que el usuario ya no existe en la base de datos
        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    /**
     * Comprueba que no deja borrar la cuenta si el usuario escribe mal su contraseña.
     *
     * @return void
     */
    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        // Creamos un usuario de prueba
        $user = User::factory()->create();

        // Intentamos borrar la cuenta poniendo una contraseña incorrecta
        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        // Verificamos que devuelve un error de validación y redirige de nuevo al perfil
        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        // Comprobamos que el usuario sigue existiendo en la base de datos
        $this->assertNotNull($user->fresh());
    }
}
