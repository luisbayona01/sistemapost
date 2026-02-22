<?php

namespace Tests\Feature;

use App\Models\Caja;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CajaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Empresa $empresa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->empresa = Empresa::factory()->create();
        $this->user = User::factory()->create(['empresa_id' => $this->empresa->id]);
    }

    /**
     * TEST 1: Crear caja - Valida empresa
     *
     * RIESGO: Caja creada sin empresa_id correcto
     */
    public function test_crear_caja_con_empresa_id()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('cajas.store'), [
            'monto_inicial' => 1000,
        ]);

        $response->assertRedirect(route('movimientos.index'));

        // EXPECTED: Caja tiene empresa_id correcto
        $caja = Caja::latest()->first();
        $this->assertEquals($this->empresa->id, $caja->empresa_id);
        $this->assertEquals($this->user->id, $caja->user_id);
        $this->assertEquals('abierta', $caja->estado);
    }

    /**
     * TEST 2: Bloquear 2da caja si ya existe abierta
     *
     * VALIDACIÓN: Usuario no puede tener 2 cajas abiertas por empresa
     */
    public function test_bloquear_segunda_caja_abierta_por_empresa()
    {
        $this->actingAs($this->user);

        // Crear 1ª caja
        $this->post(route('cajas.store'), ['monto_inicial' => 1000]);

        // Intenta crear 2ª caja
        $response = $this->post(route('cajas.store'), ['saldo_initial' => 500]);

        $response->assertRedirect(route('cajas.index'));
        $response->assertSessionHas('error');

        // EXPECTED: Solo 1 caja abierta
        $cajas = Caja::where('user_id', $this->user->id)
            ->where('estado', 'abierta')
            ->count();

        $this->assertEquals(1, $cajas);
    }

    /**
     * TEST 3: Ver caja - Validar autorización
     *
     * RIESGO: Usuario A ve caja de Usuario B
     */
    public function test_usuario_no_puede_ver_caja_ajena()
    {
        $user2 = User::factory()->create(['empresa_id' => $this->empresa->id]);

        $cajaUser2 = Caja::factory()->create([
            'empresa_id' => $this->empresa->id,
            'user_id' => $user2->id,
        ]);

        $this->actingAs($this->user);

        // Intenta ver caja de user2
        $response = $this->get(route('cajas.show', $cajaUser2->id));

        // EXPECTED: Rechaza con 403
        $response->assertStatus(403);
    }

    /**
     * TEST 4: Index solo muestra cajas del usuario
     *
     * VALIDACIÓN: Filtrado correcto por user_id
     */
    public function test_index_muestra_solo_cajas_usuario()
    {
        $user2 = User::factory()->create(['empresa_id' => $this->empresa->id]);

        // User1 crea caja
        $this->actingAs($this->user);
        Caja::factory()->create([
            'empresa_id' => $this->empresa->id,
            'user_id' => $this->user->id,
        ]);

        // User2 crea caja
        $this->actingAs($user2);
        Caja::factory()->create([
            'empresa_id' => $this->empresa->id,
            'user_id' => $user2->id,
        ]);

        // User1 ve index
        $this->actingAs($this->user);
        $response = $this->get(route('cajas.index'));

        // EXPECTED: Solo ve 1 caja (la propia)
        $this->assertCount(1, $response['cajas']);
    }

    /**
     * TEST 5: Validación de saldo inicial
     *
     * VALIDACIÓN: Saldo no puede ser negativo
     */
    public function test_monto_inicial_no_puede_ser_negativo()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('cajas.store'), [
            'monto_inicial' => -100,
        ]);

        $response->assertSessionHasErrors('monto_inicial');
    }

    /**
     * TEST 6: Cierre de caja - Validar empresa
     *
     * RIESGO: Usuario de otra empresa cierra caja ajena
     */
    public function test_usuario_no_puede_cerrar_caja_ajena()
    {
        $empresa2 = Empresa::factory()->create();
        $user2 = User::factory()->create(['empresa_id' => $empresa2->id]);

        $cajaEmpresa2 = Caja::factory()->create([
            'empresa_id' => $empresa2->id,
            'user_id' => $user2->id,
        ]);

        $this->actingAs($this->user); // Usuario de empresa1

        // Intenta actualizar caja de empresa2
        $response = $this->put(route('cajas.update', $cajaEmpresa2->id), [
            'estado' => 'cerrada',
        ]);

        // EXPECTED: Rechaza
        $response->assertStatus(403);
    }
}
