<?php

namespace Tests\Feature\MultiTenant;

use App\Models\Empresa;
use App\Models\Venta;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_data_leak_between_tenants()
    {
        // 1. Crear Tenant A con su usuario y una venta
        $tenantA = Empresa::factory()->create();
        $userA = User::factory()->create(['empresa_id' => $tenantA->id]);

        // Simular que el middleware resolvió el tenant
        app()->instance('currentTenant', $tenantA);

        $this->actingAs($userA);
        Venta::factory()->create([
            'empresa_id' => $tenantA->id,
            'user_id' => $userA->id,
            'total' => 100
        ]);

        // Verificar que Tenant A ve su venta
        $this->assertEquals(1, Venta::count(), 'Tenant A debe ver su propia venta');

        // 2. Crear Tenant B con su usuario
        $tenantB = Empresa::factory()->create();
        $userB = User::factory()->create(['empresa_id' => $tenantB->id]);

        // Simular cambio de contexto a Tenant B
        app()->instance('currentTenant', $tenantB);
        $this->actingAs($userB);

        // Verificar que Tenant B NO ve la venta de A (Aislamiento)
        $this->assertEquals(0, Venta::count(), 'Tenant B NO debe ver datos de Tenant A');

        // 3. Verificar que el scope funciona correctamente en queries crudas (explicitamente pidiendo sin scopes)
        $this->assertEquals(1, Venta::withoutGlobalScopes()->where('empresa_id', $tenantA->id)->count());
    }
}
