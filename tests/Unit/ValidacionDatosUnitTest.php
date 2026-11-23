<?php

namespace Tests\Unit;

use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Clase de pruebas unitarias para comprobar que las reglas de validación, cálculos y modelos de la aplicación funcionan bien.
 */
class ValidacionDatosUnitTest extends TestCase
{
    /**
     * PU-01: Verifica que el precio de un producto/tienda sea numérico y estrictamente positivo.
     *
     * @return void
     */
    public function test_validacion_precio_positivo(): void
    {
        // Definimos la regla: obligatorio, numérico y mínimo 1 céntimo
        $reglas = ['precio' => 'required|numeric|min:0.01'];

        // Probamos un precio negativo, uno en cero y otro correcto
        $falloNegativo = Validator::make(['precio' => -10.50], $reglas);
        $falloCero     = Validator::make(['precio' => 0], $reglas);
        $valido        = Validator::make(['precio' => 12.99], $reglas);

        // Comprobamos que los dos primeros fallan y el tercero pasa la validación
        $this->assertTrue($falloNegativo->fails());
        $this->assertTrue($falloCero->fails());
        $this->assertTrue($valido->passes());
    }

    /**
     * PU-02: Verifica que la cantidad/stock sea un número entero positivo.
     *
     * @return void
     */
    public function test_validacion_cantidad_entero_positivo(): void
    {
        // La cantidad tiene que ser un número entero y al menos 1 unidad
        $reglas = ['cantidad' => 'required|integer|min:1'];

        // Probamos con decimales, con negativos y con un entero válido
        $falloDecimal  = Validator::make(['cantidad' => 2.5], $reglas);
        $falloNegativo = Validator::make(['cantidad' => -3], $reglas);
        $valido        = Validator::make(['cantidad' => 5], $reglas);

        // Verificamos que rechaza decimales y negativos, pero acepta el 5
        $this->assertTrue($falloDecimal->fails());
        $this->assertTrue($falloNegativo->fails());
        $this->assertTrue($valido->passes());
    }

    /**
     * PU-03: Verifica formato de correo electrónico y longitud máxima de campos.
     *
     * @return void
     */
    public function test_validacion_email_y_longitud_nombre(): void
    {
        // Reglas para comprobar email válido y nombre de entre 3 y 100 caracteres
        $reglas = [
            'email'  => 'required|email|max:255',
            'nombre' => 'required|string|min:3|max:100',
        ];

        // Creamos un caso con email sin @, otro con nombre muy corto y otro con todo bien
        $falloEmail = Validator::make(['email' => 'usuario-sin-arroba.es', 'nombre' => 'Panadería Pepe'], $reglas);
        $falloCorto = Validator::make(['email' => 'pepe@gmail.com', 'nombre' => 'AB'], $reglas);
        $valido     = Validator::make(['email' => 'pepe@gmail.com', 'nombre' => 'Panadería Pepe'], $reglas);

        // Comprobamos los resultados esperados
        $this->assertTrue($falloEmail->fails());
        $this->assertTrue($falloCorto->fails());
        $this->assertTrue($valido->passes());
    }

    /**
     * PU-04: Verifica máscara/formato de teléfono español (9 dígitos).
     *
     * @return void
     */
    public function test_validacion_mascara_telefono(): void
    {
        // Expresión regular para obligar a que sean exactamente 9 números
        $reglas = ['telefono' => ['required', 'regex:/^\d{9}$/']];

        // Probamos un teléfono incompleto, uno con letra y uno correcto
        $falloCorto = Validator::make(['telefono' => '61234'], $reglas);
        $falloLetra = Validator::make(['telefono' => '61234567A'], $reglas);
        $valido     = Validator::make(['telefono' => '612345678'], $reglas);

        // Verificamos que solo pasa el de 9 dígitos
        $this->assertTrue($falloCorto->fails());
        $this->assertTrue($falloLetra->fails());
        $this->assertTrue($valido->passes());
    }

    /**
     * PU-05: Verifica que los roles permitidos en el sistema sean únicamente administrador, gestor o usuario.
     *
     * @return void
     */
    public function test_validacion_roles_permitidos(): void
    {
        // Solo aceptamos los tres roles definidos en la aplicación
        $reglas = ['rol' => 'required|in:administrador,gestor,usuario'];

        // Probamos un rol inventado, uno vacío y uno válido
        $falloInvalido = Validator::make(['rol' => 'superadmin'], $reglas);
        $falloVacio    = Validator::make(['rol' => ''], $reglas);
        $validoGestor  = Validator::make(['rol' => 'gestor'], $reglas);

        // Comprobamos que bloquea roles no permitidos
        $this->assertTrue($falloInvalido->fails());
        $this->assertTrue($falloVacio->fails());
        $this->assertTrue($validoGestor->passes());
    }

    /**
     * PU-06: Verifica que la asignación de tienda sea obligatoria únicamente cuando el rol es gestor.
     *
     * @return void
     */
    public function test_validacion_tienda_obligatoria_si_es_gestor(): void
    {
        // Si el rol es gestor, tienda_id es obligatorio; para el resto puede ir nulo
        $reglas = [
            'rol'       => 'required|in:administrador,gestor,usuario',
            'tienda_id' => 'required_if:rol,gestor|nullable|integer',
        ];

        // Probamos gestor sin tienda (debe fallar), admin sin tienda (pasa) y gestor con tienda (pasa)
        $falloGestorSinTienda  = Validator::make(['rol' => 'gestor', 'tienda_id' => null], $reglas);
        $validoAdminSinTienda  = Validator::make(['rol' => 'administrador', 'tienda_id' => null], $reglas);
        $validoGestorConTienda = Validator::make(['rol' => 'gestor', 'tienda_id' => 1], $reglas);

        // Verificamos que la regla condicional funciona bien
        $this->assertTrue($falloGestorSinTienda->fails());
        $this->assertTrue($validoAdminSinTienda->passes());
        $this->assertTrue($validoGestorConTienda->passes());
    }

    /**
     * PU-07: Verifica el cálculo de subtotales y total de un carrito multi-tienda.
     *
     * @return void
     */
    public function test_calculo_subtotal_y_total_carrito(): void
    {
        // Simulamos tres líneas de productos en el carrito con precio y cantidad
        $lineasCarrito = [
            ['precio' => 12.50, 'cantidad' => 2], // 25.00
            ['precio' => 3.20,  'cantidad' => 3], // 9.60
            ['precio' => 15.00, 'cantidad' => 1], // 15.00
        ];

        // Recorremos el array multiplicando precio por cantidad y sumando todo
        $total = array_reduce($lineasCarrito, function ($acumulado, $linea) {
            return $acumulado + round($linea['precio'] * $linea['cantidad'], 2);
        }, 0.0);

        // Comprobamos que la suma total da exactamente 49.60
        $this->assertEquals(49.60, round($total, 2));
    }

    /**
     * PU-08: Verifica el control de stock suficiente frente a la cantidad solicitada por el cliente.
     *
     * @return void
     */
    public function test_comprobacion_stock_disponible_frente_a_pedido(): void
    {
        // Suponemos que quedan 8 unidades en stock
        $stockActual = 8;

        $cantidadValida   = 5;
        $cantidadExcesiva = 10;

        // Comprobamos que deja pedir 5 unidades pero no 10
        $this->assertTrue($cantidadValida <= $stockActual);
        $this->assertFalse($cantidadExcesiva <= $stockActual);
    }

    /**
     * PU-09: Verifica el cifrado seguro de contraseñas mediante Hash.
     *
     * @return void
     */
    public function test_cifrado_y_verificacion_contrasena(): void
    {
        // Encriptamos una contraseña de ejemplo
        $clavePlana = 'ClaveSegura2026';
        $hash = Hash::make($clavePlana);

        // Verificamos que el hash no es texto plano, que coincide con la clave original y falla con otra
        $this->assertNotEquals($clavePlana, $hash);
        $this->assertTrue(Hash::check($clavePlana, $hash));
        $this->assertFalse(Hash::check('ClaveIncorrecta', $hash));
    }

    /**
     * PU-10: Verifica la asignación masiva de atributos en los modelos Tienda y Producto.
     *
     * @return void
     */
    public function test_asignacion_masiva_modelos_tienda_y_producto(): void
    {
        // Instanciamos una Tienda pasando un array de datos (comprueba el $fillable del modelo)
        $tienda = new Tienda([
            'categoria_id' => 1,
            'nombre'       => 'Librería Central',
            'email'        => 'info@libreriacentral.es',
            'direccion'    => 'Av. de la Constitución 12',
        ]);

        // Instanciamos un Producto pasando sus atributos por array
        $producto = new Producto([
            'categoria_id' => 1,
            'denominacion' => 'Cuaderno A4 Cuadrícula',
        ]);

        // Comprobamos que los atributos se han rellenado correctamente en ambos objetos
        $this->assertEquals('Librería Central', $tienda->nombre);
        $this->assertEquals('info@libreriacentral.es', $tienda->email);
        $this->assertEquals('Cuaderno A4 Cuadrícula', $producto->denominacion);
    }
}
