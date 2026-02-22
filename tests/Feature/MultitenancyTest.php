<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use App\Models\Venta;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultitenancyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Deshabilitar observers para tests (evita validación de caja)
        Venta::unsetEventDispatcher();
    }

    /**
     * Test: Usuario solo puede ver ventas de su empresa
     */
    public function test_usuario_no_puede_ver_ventas_de_otra_empresa(): void
    {
        // Crear dos empresas
        $empresaA = Empresa::factory()->create(['nombre' => 'Cine A']);
        $empresaB = Empresa::factory()->create(['nombre' => 'Cine B']);

        // Crear usuarios para cada empresa
        $userA = User::factory()->create(['empresa_id' => $empresaA->id]);
        $userB = User::factory()->create(['empresa_id' => $empresaB->id]);

        // Crear ventas para cada empresa
        $ventaA = Venta::factory()->create([
            'empresa_id' => $empresaA->id,
            'user_id' => $userA->id,
        ]);
        $ventaB = Venta::factory()->create([
            'empresa_id' => $empresaB->id,
            'user_id' => $userB->id,
        ]);

        // Autenticar como usuario A
        $this->actingAs($userA);

        // Usuario A solo debe ver su venta (Global Scope activo)
        $ventasVisibles = Venta::all();

        $this->assertEquals(1, $ventasVisibles->count(), 'Usuario A debe ver solo 1 venta');
        $this->assertTrue($ventasVisibles->first()->is($ventaA), 'La venta visible debe ser de Empresa A');
        $this->assertFalse($ventasVisibles->contains($ventaB), 'No debe ver ventas de Empresa B');
    }

    /**
     * Test: Usuario solo puede ver productos de su empresa
     */
    public function test_usuario_no_puede_ver_productos_de_otra_empresa(): void
    {
        $empresaA = Empresa::factory()->create();
        $empresaB = Empresa::factory()->create();

        $userA = User::factory()->create(['empresa_id' => $empresaA->id]);

        $productoA = Producto::factory()->create([
            'empresa_id' => $empresaA->id,
            'nombre' => 'Coca-Cola A'
        ]);
        $productoB = Producto::factory()->create([
            'empresa_id' => $empresaB->id,
            'nombre' => 'Coca-Cola B'
        ]);

        $this->actingAs($userA);

        $productosVisibles = Producto::all();

        $this->assertEquals(1, $productosVisibles->count(), 'Usuario A debe ver solo 1 producto');
        $this->assertEquals('Coca-Cola A', $productosVisibles->first()->nombre);
    }

    /**
     * Test: Dashboard solo muestra datos de la empresa del usuario
     */
    public function test_dashboard_solo_muestra_datos_de_empresa_actual(): void
    {
        $empresaA = Empresa::factory()->create();
        $empresaB = Empresa::factory()->create();

        $userA = User::factory()->create(['empresa_id' => $empresaA->id]);

        // Crear ventas para ambas empresas
        Venta::factory()->count(5)->create([
            'empresa_id' => $empresaA->id,
            'user_id' => $userA->id,
            'total' => 100,
        ]);

        $userB = User::factory()->create(['empresa_id' => $empresaB->id]);
        Venta::factory()->count(3)->create([
            'empresa_id' => $empresaB->id,
            'user_id' => $userB->id,
            'total' => 200,
        ]);

        $this->actingAs($userA);

        // Simular consulta del Dashboard
        $totalVentas = Venta::sum('total');

        // Solo debe sumar las ventas de Empresa A (5 * 100 = 500)
        $this->assertEquals(500, $totalVentas, 'Dashboard debe mostrar solo ventas de Empresa A');
    }

    /**
     * Test: Validar que Global Scope se aplica automáticamente
     */
    public function test_global_scope_se_aplica_automaticamente(): void
    {
        $empresaA = Empresa::factory()->create();
        $empresaB = Empresa::factory()->create();

        $userA = User::factory()->create(['empresa_id' => $empresaA->id]);

        // Crear 3 productos para Empresa A y 2 para Empresa B
        Producto::factory()->count(3)->create(['empresa_id' => $empresaA->id]);
        Producto::factory()->count(2)->create(['empresa_id' => $empresaB->id]);

        $this->actingAs($userA);

        // Sin especificar where, debe aplicar Global Scope
        $productosVisibles = Producto::count();

        $this->assertEquals(3, $productosVisibles, 'Global Scope debe filtrar automáticamente por empresa_id');
    }
}
