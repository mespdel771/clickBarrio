<?php

namespace Tests\Feature;

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
}
