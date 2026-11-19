<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlujosClickBarrioFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PI-01: La portada carga correctamente y un usuario autenticado accede a tiendas y categorías
     */
    public function test_acceso_a_portada_y_escaparate_autenticado(): void
    {
        $this->get('/')->assertStatus(200);

        $cliente = User::factory()->create();
        $this->actingAs($cliente)->get(route('tiendas.index'))->assertStatus(200);
        $this->actingAs($cliente)->get(route('categorias.index'))->assertStatus(200);
    }

    /**
     * PI-02: Un usuario no autenticado es redirigido al login si intenta ver rutas privadas
     */
    public function test_invitado_redirigido_a_login_en_rutas_protegidas(): void
    {
        $this->get(route('tiendas.index'))->assertRedirect('/login');
        $this->get(route('categorias.index'))->assertRedirect('/login');
        $this->get(route('carrito.index'))->assertRedirect('/login');
        $this->get(route('productos.index'))->assertRedirect('/login');
        $this->get(route('reservas.index'))->assertRedirect('/login');
    }

    /**
     * PI-03: Control de acceso por roles (Un cliente no puede gestionar usuarios ni crear tiendas)
     */
    public function test_cliente_no_puede_acceder_a_rutas_de_administrador_ni_gestor(): void
    {
        $cliente = User::factory()->create();

        $this->actingAs($cliente)->get(route('usuarios.index'))->assertStatus(403);
        $this->actingAs($cliente)->get(route('tiendas.create'))->assertStatus(403);
        $this->actingAs($cliente)->get(route('ingresos.index'))->assertStatus(403);
    }

    /**
     * PI-04: Un administrador sí tiene acceso a la gestión de usuarios y creación de tiendas
     */
    public function test_administrador_accede_a_gestion_global(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['administrador']);

        $this->actingAs($admin)->get(route('usuarios.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('tiendas.create'))->assertStatus(200);
    }

    /**
     * PI-05: Un gestor puede ver el panel de ingresos pero no puede crear tiendas nuevas
     */
    public function test_gestor_accede_a_ingresos_pero_no_a_crear_tienda(): void
    {
        $gestor = User::factory()->create();
        $gestor->syncRoles(['gestor']);

        $this->actingAs($gestor)->get(route('ingresos.index'))->assertStatus(200);
        $this->actingAs($gestor)->get(route('tiendas.create'))->assertStatus(403);
    }

    /**
     * PI-06: El sistema rechaza el envío de formularios con datos vacíos o erróneos
     */
    public function test_administrador_recibe_errores_al_crear_categoria_vacia(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['administrador']);

        $response = $this->actingAs($admin)->post(route('categorias.store'), [
            'denominacion' => '', // Campo obligatorio vacío
        ]);

        $response->assertSessionHasErrors(['denominacion']);
    }

    /**
     * PI-07: Crear una tienda exige correo electrónico obligatorio y evita excepción SQL
     */
    public function test_crear_tienda_requiere_email_obligatorio(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['administrador']);

        $categoria = Categoria::create(['denominacion' => 'Ferretería']);

        // Intento sin email -> debe devolver error de validación en lugar de excepción SQL
        $this->actingAs($admin)
            ->post(route('tiendas.store'), [
                'categoria_id' => $categoria->id,
                'nombre'       => 'Ferretería El Martillo',
                'direccion'    => 'Calle Mayor 1',
            ])
            ->assertSessionHasErrors(['email']);

        // Intento con email válido -> debe guardarse correctamente
        $this->actingAs($admin)
            ->post(route('tiendas.store'), [
                'categoria_id' => $categoria->id,
                'nombre'       => 'Ferretería El Martillo',
                'email'        => 'contacto@elmartillo.es',
                'direccion'    => 'Calle Mayor 1',
            ])
            ->assertRedirect(route('tiendas.index'));

        $this->assertDatabaseHas('tiendas', [
            'nombre' => 'Ferretería El Martillo',
            'email'  => 'contacto@elmartillo.es',
        ]);
    }

    /**
     * PI-08: No se puede crear un usuario con rol gestor sin asignarle una tienda
     */
    public function test_crear_gestor_requiere_tienda_asignada(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['administrador']);

        $this->actingAs($admin)
            ->post(route('usuarios.store'), [
                'name'     => 'Gestor Sin Tienda',
                'email'    => 'gestorsintienda@ejemplo.com',
                'password' => 'password123',
                'rol'      => 'gestor',
            ])
            ->assertSessionHasErrors(['tienda_id']);

        $this->assertDatabaseMissing('users', [
            'email' => 'gestorsintienda@ejemplo.com',
        ]);
    }

    /**
     * PI-09: Al asignar un nuevo gestor a una tienda ocupada, el antiguo gestor pasa a rol usuario
     */
    public function test_reemplazar_gestor_degrada_al_anterior_a_rol_usuario(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['administrador']);

        $antiguoGestor = User::factory()->create();
        $antiguoGestor->syncRoles(['gestor']);

        $categoria = Categoria::create(['denominacion' => 'Alimentación']);
        $tienda = Tienda::create([
            'categoria_id' => $categoria->id,
            'nombre'       => 'Panadería La Espiga',
            'email'        => 'panaderia@ejemplo.com',
            'user_id'      => $antiguoGestor->id,
        ]);

        // Creamos el nuevo gestor asignándole la tienda que ya tenía $antiguoGestor
        $this->actingAs($admin)
            ->post(route('usuarios.store'), [
                'name'      => 'Nuevo Gestor',
                'email'     => 'nuevogestor@ejemplo.com',
                'password'  => 'password123',
                'rol'       => 'gestor',
                'tienda_id' => $tienda->id,
            ])
            ->assertRedirect(route('usuarios.index'));

        $antiguoGestor->refresh();
        $tienda->refresh();

        $this->assertTrue($antiguoGestor->hasRole('usuario'));
        $this->assertFalse($antiguoGestor->hasRole('gestor'));
        $this->assertEquals('nuevogestor@ejemplo.com', $tienda->user->email);
    }

    /**
     * PI-10: Un gestor no puede incorporar un producto a su tienda sin precio y stock obligatorios
     */
    public function test_gestor_debe_indicar_precio_y_stock_al_incorporar_producto(): void
    {
        $gestor = User::factory()->create();
        $gestor->syncRoles(['gestor']);

        $categoria = Categoria::create(['denominacion' => 'Electrónica']);
        $tienda = Tienda::create([
            'categoria_id' => $categoria->id,
            'nombre'       => 'Tech Barrio',
            'email'        => 'tech@ejemplo.com',
            'user_id'      => $gestor->id,
        ]);

        $producto = Producto::create([
            'categoria_id' => $categoria->id,
            'denominacion' => 'Auriculares Bluetooth',
        ]);

        // Intento activando la tienda pero sin precio ni stock -> error de validación
        $this->actingAs($gestor)
            ->put(route('productos.update', $producto), [
                'denominacion' => 'Auriculares Bluetooth',
                'categoria_id' => $categoria->id,
                'tiendas'      => [
                    $tienda->id => [
                        'activo' => '1',
                        'precio' => '',
                        'stock'  => '',
                    ],
                ],
            ])
            ->assertSessionHasErrors(["tiendas.{$tienda->id}.precio"]);

        // Intento con precio y stock válidos -> se guarda correctamente
        $this->actingAs($gestor)
            ->put(route('productos.update', $producto), [
                'denominacion' => 'Auriculares Bluetooth',
                'categoria_id' => $categoria->id,
                'tiendas'      => [
                    $tienda->id => [
                        'activo' => '1',
                        'precio' => '29.95',
                        'stock'  => '10',
                    ],
                ],
            ])
            ->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('precios', [
            'producto_id' => $producto->id,
            'tienda_id'   => $tienda->id,
            'precio'      => 29.95,
        ]);

        $this->assertDatabaseHas('stocks', [
            'producto_id' => $producto->id,
            'tienda_id'   => $tienda->id,
            'stock'       => 10,
        ]);
    }
}
