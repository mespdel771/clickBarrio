<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidacionDatosUnitTest extends TestCase
{
    /**
     * PU-01: Verifica que el precio de un producto/tienda sea numérico y estrictamente positivo
     */
    public function test_validacion_precio_positivo(): void
    {
        $reglas = ['precio' => 'required|numeric|min:0.01'];

        $falloNegativo = Validator::make(['precio' => -10.50], $reglas);
        $falloCero     = Validator::make(['precio' => 0], $reglas);
        $valido        = Validator::make(['precio' => 12.99], $reglas);

        $this->assertTrue($falloNegativo->fails());
        $this->assertTrue($falloCero->fails());
        $this->assertTrue($valido->passes());
    }

    /**
     * PU-02: Verifica que la cantidad/stock sea un número entero positivo
     */
    public function test_validacion_cantidad_entero_positivo(): void
    {
        $reglas = ['cantidad' => 'required|integer|min:1'];

        $falloDecimal  = Validator::make(['cantidad' => 2.5], $reglas);
        $falloNegativo = Validator::make(['cantidad' => -3], $reglas);
        $valido        = Validator::make(['cantidad' => 5], $reglas);

        $this->assertTrue($falloDecimal->fails());
        $this->assertTrue($falloNegativo->fails());
        $this->assertTrue($valido->passes());
    }

    /**
     * PU-03: Verifica formato de correo electrónico y longitud máxima de campos
     */
    public function test_validacion_email_y_longitud_nombre(): void
    {
        $reglas = [
            'email'  => 'required|email|max:255',
            'nombre' => 'required|string|min:3|max:100',
        ];

        $falloEmail = Validator::make(['email' => 'usuario-sin-arroba.es', 'nombre' => 'Panadería Pepe'], $reglas);
        $falloCorto = Validator::make(['email' => 'pepe@gmail.com', 'nombre' => 'AB'], $reglas);
        $valido     = Validator::make(['email' => 'pepe@gmail.com', 'nombre' => 'Panadería Pepe'], $reglas);

        $this->assertTrue($falloEmail->fails());
        $this->assertTrue($falloCorto->fails());
        $this->assertTrue($valido->passes());
    }

    /**
     * PU-04: Verifica máscara/formato de teléfono español (9 dígitos)
     */
    public function test_validacion_mascara_telefono(): void
    {
        $reglas = ['telefono' => ['required', 'regex:/^\d{9}$/']];


        $falloCorto = Validator::make(['telefono' => '61234'], $reglas);
        $falloLetra = Validator::make(['telefono' => '61234567A'], $reglas);
        $valido     = Validator::make(['telefono' => '612345678'], $reglas);

        $this->assertTrue($falloCorto->fails());
        $this->assertTrue($falloLetra->fails());
        $this->assertTrue($valido->passes());
    }
}
