<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Clase de prueba unitaria básica de ejemplo que viene por defecto en el proyecto.
 */
class ExampleTest extends TestCase
{
    /**
     * Prueba sencilla que simplemente comprueba que verdadero es verdadero para verificar que PHPUnit funciona.
     *
     * @return void
     */
    public function test_that_true_is_true(): void
    {
        // Verificamos que el valor pasado es true
        $this->assertTrue(true);
    }
}
