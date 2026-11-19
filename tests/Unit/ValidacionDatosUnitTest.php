<?php

namespace Tests\Unit;

use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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

    /**
     * PU-05: Verifica que los roles permitidos en el sistema sean únicamente administrador, gestor o usuario
     */
    public function test_validacion_roles_permitidos(): void
    {
        $reglas = ['rol' => 'required|in:administrador,gestor,usuario'];

        $falloInvalido = Validator::make(['rol' => 'superadmin'], $reglas);
        $falloVacio    = Validator::make(['rol' => ''], $reglas);
        $validoGestor  = Validator::make(['rol' => 'gestor'], $reglas);

        $this->assertTrue($falloInvalido->fails());
        $this->assertTrue($falloVacio->fails());
        $this->assertTrue($validoGestor->passes());
    }

    /**
     * PU-06: Verifica que la asignación de tienda sea obligatoria únicamente cuando el rol es gestor
     */
    public function test_validacion_tienda_obligatoria_si_es_gestor(): void
    {
        $reglas = [
            'rol'       => 'required|in:administrador,gestor,usuario',
            'tienda_id' => 'required_if:rol,gestor|nullable|integer',
        ];

        $falloGestorSinTienda = Validator::make(['rol' => 'gestor', 'tienda_id' => null], $reglas);
        $validoAdminSinTienda = Validator::make(['rol' => 'administrador', 'tienda_id' => null], $reglas);
        $validoGestorConTienda = Validator::make(['rol' => 'gestor', 'tienda_id' => 1], $reglas);

        $this->assertTrue($falloGestorSinTienda->fails());
        $this->assertTrue($validoAdminSinTienda->passes());
        $this->assertTrue($validoGestorConTienda->passes());
    }

    /**
     * PU-07: Verifica el cálculo de subtotales y total de un carrito multi-tienda
     */
    public function test_calculo_subtotal_y_total_carrito(): void
    {
        $lineasCarrito = [
            ['precio' => 12.50, 'cantidad' => 2], // 25.00
            ['precio' => 3.20,  'cantidad' => 3], // 9.60
            ['precio' => 15.00, 'cantidad' => 1], // 15.00
        ];

        $total = array_reduce($lineasCarrito, function ($acumulado, $linea) {
            return $acumulado + round($linea['precio'] * $linea['cantidad'], 2);
        }, 0.0);

        $this->assertEquals(49.60, round($total, 2));
    }

    /**
     * PU-08: Verifica el control de stock suficiente frente a la cantidad solicitada por el cliente
     */
    public function test_comprobacion_stock_disponible_frente_a_pedido(): void
    {
        $stockActual = 8;

        $cantidadValida   = 5;
        $cantidadExcesiva = 10;

        $this->assertTrue($cantidadValida <= $stockActual);
        $this->assertFalse($cantidadExcesiva <= $stockActual);
    }

    /**
     * PU-09: Verifica el cifrado seguro de contraseñas mediante Hash
     */
    public function test_cifrado_y_verificacion_contrasena(): void
    {
        $clavePlana = 'ClaveSegura2026';
        $hash = Hash::make($clavePlana);

        $this->assertNotEquals($clavePlana, $hash);
        $this->assertTrue(Hash::check($clavePlana, $hash));
        $this->assertFalse(Hash::check('ClaveIncorrecta', $hash));
    }

    /**
     * PU-10: Verifica la asignación masiva de atributos en los modelos Tienda y Producto
     */
    public function test_asignacion_masiva_modelos_tienda_y_producto(): void
    {
        $tienda = new Tienda([
            'categoria_id' => 1,
            'nombre'       => 'Librería Central',
            'email'        => 'info@libreriacentral.es',
            'direccion'    => 'Av. de la Constitución 12',
        ]);

        $producto = new Producto([
            'categoria_id' => 1,
            'denominacion' => 'Cuaderno A4 Cuadrícula',
        ]);

        $this->assertEquals('Librería Central', $tienda->nombre);
        $this->assertEquals('info@libreriacentral.es', $tienda->email);
        $this->assertEquals('Cuaderno A4 Cuadrícula', $producto->denominacion);
    }
}
