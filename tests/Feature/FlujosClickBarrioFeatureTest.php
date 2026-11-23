<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Clase de pruebas de integración para comprobar los flujos principales de la aplicación:
 * control de acceso por roles, creación de tiendas, gestión de gestores y catálogo.
 */
class FlujosClickBarrioFeatureTest extends TestCase
{
    // Limpia y reinicia la base de datos antes de cada prueba para que no se mezclen registros
    use RefreshDatabase;

    /**
     * PI-01: La portada carga correctamente y un usuario autenticado accede a tiendas y categorías.
     *
     * @return void
     */
    public function test_acceso_a_portada_y_escaparate_autenticado(): void
    {
        // Comprobamos que la página principal carga sin necesidad de estar logueado
        $this->get('/')->assertStatus(200);

        // Creamos un cliente de prueba y verificamos que puede entrar a ver tiendas y categorías
        $cliente = User::factory()->create();
        $this->actingAs($cliente)->get(route('tiendas.index'))->assertStatus(200);
        $this->actingAs($cliente)->get(route('categorias.index'))->assertStatus(200);
    }

    /**
     * PI-02: Un usuario no autenticado es redirigido al login si intenta ver rutas privadas.
     *
     * @return void
     */
    public function test_invitado_redirigido_a_login_en_rutas_protegidas(): void
    {
        // Probamos a entrar sin iniciar sesión a las rutas protegidas y comprobamos que nos manda al login
        $this->get(route('tiendas.index'))->assertRedirect('/login');
        $this->get(route('categorias.index'))->assertRedirect('/login');
        $this->get(route('carrito.index'))->assertRedirect('/login');
        $this->get(route('productos.index'))->assertRedirect('/login');
        $this->get(route('reservas.index'))->assertRedirect('/login');
    }

    /**
     * PI-03: Control de acceso por roles (Un cliente no puede gestionar usuarios ni crear tiendas).
     *
     * @return void
     */
    public function test_cliente_no_puede_acceder_a_rutas_de_administrador_ni_gestor(): void
    {
        // Creamos un usuario normal (cliente)
        $cliente = User::factory()->create();

        // Verificamos que le devuelve un error 403 (Prohibido) si intenta entrar en rutas de admin o gestor
        $this->actingAs($cliente)->get(route('usuarios.index'))->assertStatus(403);
        $this->actingAs($cliente)->get(route('tiendas.create'))->assertStatus(403);
        $this->actingAs($cliente)->get(route('ingresos.index'))->assertStatus(403);
    }

    /**
     * PI-04: Un administrador sí tiene acceso a la gestión de usuarios y creación de tiendas.
     *
     * @return void
     */
    public function test_administrador_accede_a_gestion_global(): void
    {
        // Creamos un usuario y le asignamos el rol de administrador
        $admin = User::factory()->create();
        $admin->syncRoles(['administrador']);

        // Comprobamos que puede entrar sin problemas a la gestión de usuarios y al formulario de nueva tienda
        $this->actingAs($admin)->get(route('usuarios.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('tiendas.create'))->assertStatus(200);
    }

    /**
     * PI-05: Un gestor puede ver el panel de ingresos pero no puede crear tiendas nuevas.
     *
     * @return void
     */
    public function test_gestor_accede_a_ingresos_pero_no_a_crear_tienda(): void
    {
        // Creamos un usuario con rol de gestor
        $gestor = User::factory()->create();
        $gestor->syncRoles(['gestor']);

        // Puede ver sus ingresos (200), pero no tiene permiso para dar de alta tiendas nuevas (403)
        $this->actingAs($gestor)->get(route('ingresos.index'))->assertStatus(200);
        $this->actingAs($gestor)->get(route('tiendas.create'))->assertStatus(403);
    }

    /**
     * PI-06: El sistema rechaza el envío de formularios con datos vacíos o erróneos.
     *
     * @return void
     */
    public function test_administrador_recibe_errores_al_crear_categoria_vacia(): void
    {
        // Preparamos un usuario administrador
        $admin = User::factory()->create();
        $admin->syncRoles(['administrador']);

        // Intentamos crear una categoría dejando el nombre en blanco
        $response = $this->actingAs($admin)->post(route('categorias.store'), [
            'denominacion' => '', // Campo obligatorio vacío
        ]);

        // Comprobamos que devuelve un error de validación en el campo denominacion
        $response->assertSessionHasErrors(['denominacion']);
    }

    /**
     * PI-07: Crear una tienda exige correo electrónico obligatorio y evita excepción SQL.
     *
     * @return void
     */
    public function test_crear_tienda_requiere_email_obligatorio(): void
    {
        // Preparamos un administrador y una categoría de ejemplo
        $admin = User::factory()->create();
        $admin->syncRoles(['administrador']);

        $categoria = Categoria::create(['denominacion' => 'Ferretería']);

        // Intento sin email -> debe devolver error de validación en lugar de fallar en la base de datos
        $this->actingAs($admin)
            ->post(route('tiendas.store'), [
                'categoria_id' => $categoria->id,
                'nombre'       => 'Ferretería El Martillo',
                'direccion'    => 'Calle Mayor 1',
            ])
            ->assertSessionHasErrors(['email']);

        // Intento con email válido -> debe guardarse correctamente y redirigir al listado
        $this->actingAs($admin)
            ->post(route('tiendas.store'), [
                'categoria_id' => $categoria->id,
                'nombre'       => 'Ferretería El Martillo',
                'email'        => 'contacto@elmartillo.es',
                'direccion'    => 'Calle Mayor 1',
            ])
            ->assertRedirect(route('tiendas.index'));

        // Comprobamos que la tienda se ha guardado en la tabla tiendas
        $this->assertDatabaseHas('tiendas', [
            'nombre' => 'Ferretería El Martillo',
            'email'  => 'contacto@elmartillo.es',
        ]);
    }

    /**
     * PI-08: No se puede crear un usuario con rol gestor sin asignarle una tienda.
     *
     * @return void
     */
    public function test_crear_gestor_requiere_tienda_asignada(): void
    {
        // Preparamos un administrador
        $admin = User::factory()->create();
        $admin->syncRoles(['administrador']);

        // Intentamos dar de alta un gestor sin elegir tienda
        $this->actingAs($admin)
            ->post(route('usuarios.store'), [
                'name'     => 'Gestor Sin Tienda',
                'email'    => 'gestorsintienda@ejemplo.com',
                'password' => 'password123',
                'rol'      => 'gestor',
            ])
            ->assertSessionHasErrors(['tienda_id']);

        // Verificamos que el usuario no se ha creado en la base de datos
        $this->assertDatabaseMissing('users', [
            'email' => 'gestorsintienda@ejemplo.com',
        ]);
    }

    /**
     * PI-09: Al asignar un nuevo gestor a una tienda ocupada, el antiguo gestor pasa a rol usuario.
     *
     * @return void
     */
    public function test_reemplazar_gestor_degrada_al_anterior_a_rol_usuario(): void
    {
        // Creamos un administrador, un gestor antiguo y una tienda asignada a ese gestor
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

        // Recargamos los datos desde la base de datos
        $antiguoGestor->refresh();
        $tienda->refresh();

        // Comprobamos que el antiguo gestor ahora es un usuario normal y que la tienda pertenece al nuevo
        $this->assertTrue($antiguoGestor->hasRole('usuario'));
        $this->assertFalse($antiguoGestor->hasRole('gestor'));
        $this->assertEquals('nuevogestor@ejemplo.com', $tienda->user->email);
    }

    /**
     * PI-10: Un gestor no puede incorporar un producto a su tienda sin precio y stock obligatorios.
     *
     * @return void
     */
    public function test_gestor_debe_indicar_precio_y_stock_al_incorporar_producto(): void
    {
        // Preparamos un gestor, su tienda y un producto de su categoría
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

        // Intento activando la tienda pero sin precio ni stock -> devuelve error de validación
        $this->actingAs($gestor)
            ->put(route('productos.update', $producto), [
