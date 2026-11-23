<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clase de prueba básica para verificar que la página principal de la aplicación funciona.
 */
class ExampleTest extends TestCase
{
    /**
     * Comprueba que al entrar a la ruta inicial (/) la aplicación responde correctamente.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Hacemos una petición GET a la página de inicio
        $response = $this->get('/');

        // Verificamos que la respuesta es correcta (código 200)
        $response->assertStatus(200);
    }
}
