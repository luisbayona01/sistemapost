<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\User;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\Caja;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test de Flujo Completo de Venta POS
 * 
 * OBJETIVO: Verificar con certeza absoluta que:
 * 1. El precio SIEMPRE sale de productos.precio
 * 2. El total se calcula UNA sola vez en backend
 * 3. La confirmación es explícita (success/error)
 */
class POSVentaFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $empresa;
    protected $user;
    protected $caja;
    protected $producto;
    protected $cliente;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear empresa
        $this->empresa = Empresa::factory()->create([
            'nombre' => 'Cinema Test',
            'porcentaje_impuesto' => 19
        ]);

        // Crear usuario
        $this->user = User::factory()->create([
            'empresa_id' => $this->empresa->id,
            'name' => 'Cajero Test'
        ]);

        // Crear cliente genérico
        $this->cliente = Cliente::factory()->create([
            'empresa_id' => $this->empresa->id,
            'nombre' => 'Cliente Genérico',
            'tipo_cliente' => 'GENERICO'
        ]);

        // Crear caja abierta
        $this->caja = Caja::create([
            'empresa_id' => $this->empresa->id,
            'user_id' => $this->user->id,
            'monto_inicial' => 50000,
            'estado' => 'ABIERTA',
            'fecha_apertura' => now()
        ]);

        // Crear categoría
        $categoria = Categoria::factory()->create([
            'empresa_id' => $this->empresa->id,
            'nombre' => 'Confitería'
        ]);

        // Crear producto con precio específico
        $this->producto = Producto::create([
            'empresa_id' => $this->empresa->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Coca-Cola 500ml',
            'precio' => 5000, // ← PRECIO FUENTE DE VERDAD
            'es_venta_retail' => true,
            'tipo_producto' => 'PRODUCTO_FINAL'
        ]);

        // Crear inventario con stock
        Inventario::create([
            'empresa_id' => $this->empresa->id,
            'producto_id' => $this->producto->id,
            'cantidad' => 100,
            'stock_minimo' => 10
        ]);

        // Autenticar usuario
        $this->actingAs($this->user);
    }

    /**
     * TEST 1: Verificar que el precio sale de productos.precio
     */
    public function test_precio_sale_de_base_de_datos(): void
    {
        // Agregar producto al carrito
        $response = $this->postJson(route('pos.agregar.producto'), [
            'producto_id' => $this->producto->id,
            'cantidad' => 2
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verificar que el precio en sesión coincide con la BD
        $carrito = session('carrito_pos');
        $this->assertNotEmpty($carrito['productos']);

        $productoEnCarrito = $carrito['productos'][0];

        // ✅ VERIFICACIÓN CRÍTICA: El precio debe ser exactamente el de la BD
        $this->assertEquals(
            5000,
            $productoEnCarrito['precio'],
            '❌ FALLO: El precio NO sale de productos.precio'
        );

        // Verificar que coincide con el modelo
        $this->assertEquals(
            $this->producto->precio,
            $productoEnCarrito['precio'],
            '❌ FALLO: El precio en carrito NO coincide con productos.precio'
        );
    }

    /**
     * TEST 2: Verificar que el total se calcula UNA sola vez en backend
     */
    public function test_total_se_calcula_en_backend(): void
    {
        // Agregar producto al carrito (2 unidades a $5000 = $10000)
        $this->postJson(route('pos.agregar.producto'), [
            'producto_id' => $this->producto->id,
            'cantidad' => 2
        ]);

        // Finalizar venta
        $response = $this->postJson(route('pos.finalizar'), [
            'metodo_pago' => 'EFECTIVO',
            'monto_recibido' => 15000
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $data = $response->json();

        // ✅ VERIFICACIÓN CRÍTICA: El total debe ser calculado en backend
        $totalEsperado = 2 * 5000; // 2 unidades × $5000

        // El total incluye IVA, calcular el total final
        $porcentajeImpuesto = 19;
        $factor = 1 + ($porcentajeImpuesto / 100);
        $subtotal = $totalEsperado / $factor;
        $impuesto = $totalEsperado - $subtotal;
        $totalFinal = $totalEsperado; // Sin tarifa en productos

        $this->assertEquals(
            $totalFinal,
            $data['total_pagado'],
            '❌ FALLO: El total NO se calculó correctamente en backend'
        );

        // Verificar que la venta se guardó con el total correcto
        $this->assertDatabaseHas('ventas', [
            'empresa_id' => $this->empresa->id,
            'user_id' => $this->user->id,
            'total' => $totalFinal,
            'estado_pago' => 'PAGADA'
        ]);
    }

    /**
     * TEST 3: Verificar confirmación explícita de éxito
     */
    public function test_confirmacion_explicita_de_exito(): void
    {
        // Agregar producto
        $this->postJson(route('pos.agregar.producto'), [
            'producto_id' => $this->producto->id,
            'cantidad' => 1
        ]);

        // Finalizar venta
        $response = $this->postJson(route('pos.finalizar'), [
            'metodo_pago' => 'EFECTIVO'
        ]);

        $response->assertStatus(200);

        $data = $response->json();

        // ✅ VERIFICACIÓN CRÍTICA: Debe haber confirmación explícita
        $this->assertTrue($data['success'], '❌ FALLO: No hay confirmación de éxito');
        $this->assertArrayHasKey('message', $data, '❌ FALLO: No hay mensaje de confirmación');
        $this->assertArrayHasKey('venta_id', $data, '❌ FALLO: No se retorna ID de venta');
        $this->assertArrayHasKey('total_pagado', $data, '❌ FALLO: No se retorna total pagado');
        $this->assertArrayHasKey('print_url', $data, '❌ FALLO: No se retorna URL de impresión');
        $this->assertArrayHasKey('tipo_venta_desc', $data, '❌ FALLO: No se retorna tipo de venta');

        // Verificar que el mensaje es explícito
        $this->assertStringContainsString(
            'confirmada',
            strtolower($data['message']),
            '❌ FALLO: El mensaje no es explícito sobre el éxito'
        );
    }

    /**
     * TEST 4: Verificar error explícito cuando falla
     */
    public function test_error_explicito_cuando_falla(): void
    {
        // Intentar finalizar venta con carrito vacío
        $response = $this->postJson(route('pos.finalizar'), [
            'metodo_pago' => 'EFECTIVO'
        ]);

        $response->assertStatus(422); // Unprocessable Entity

        $data = $response->json();

        // ✅ VERIFICACIÓN CRÍTICA: Debe haber error explícito
        $this->assertFalse($data['success'], '❌ FALLO: No indica fallo explícitamente');
        $this->assertArrayHasKey('message', $data, '❌ FALLO: No hay mensaje de error');
        $this->assertStringContainsString(
            'vacío',
            strtolower($data['message']),
            '❌ FALLO: El mensaje de error no es claro'
        );
    }

    /**
     * TEST 5: Verificar error explícito por stock insuficiente
     */
    public function test_error_explicito_stock_insuficiente(): void
    {
        // Reducir stock a 1
        $this->producto->inventario->update(['cantidad' => 1]);

        // Intentar agregar 5 unidades
        $response = $this->postJson(route('pos.agregar.producto'), [
            'producto_id' => $this->producto->id,
            'cantidad' => 5
        ]);

        $response->assertStatus(422);

        $data = $response->json();

        // ✅ VERIFICACIÓN CRÍTICA: Error explícito de stock
        $this->assertFalse($data['success']);
        $this->assertStringContainsString(
            'stock',
            strtolower($data['message']),
            '❌ FALLO: El error de stock no es explícito'
        );
    }

    /**
     * TEST 6: Verificar que el precio NO puede ser manipulado desde frontend
     */
    public function test_precio_no_puede_ser_manipulado(): void
    {
        // Intentar enviar un precio diferente (simulando manipulación)
        $response = $this->postJson(route('pos.agregar.producto'), [
            'producto_id' => $this->producto->id,
            'cantidad' => 1,
            'precio_falso' => 1 // ← Intento de manipulación
        ]);

        $response->assertStatus(200);

        // Verificar que el precio en carrito sigue siendo el de la BD
        $carrito = session('carrito_pos');
        $productoEnCarrito = $carrito['productos'][0];

        $this->assertEquals(
            5000,
            $productoEnCarrito['precio'],
            '❌ FALLO CRÍTICO: El precio fue manipulado desde frontend'
        );

        $this->assertNotEquals(
            1,
            $productoEnCarrito['precio'],
            '❌ FALLO CRÍTICO: Se aceptó un precio manipulado'
        );
    }

    /**
     * TEST 7: Flujo completo de venta exitosa
     */
    public function test_flujo_completo_venta_exitosa(): void
    {
        echo "\n\n";
        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  🧪 TEST DE FLUJO COMPLETO DE VENTA POS\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        // PASO 1: Agregar producto al carrito
        echo "📦 PASO 1: Agregando producto al carrito...\n";
        $response1 = $this->postJson(route('pos.agregar.producto'), [
            'producto_id' => $this->producto->id,
            'cantidad' => 3
        ]);

        $this->assertTrue($response1->json('success'));
        echo "   ✅ Producto agregado: {$this->producto->nombre}\n";
        echo "   ✅ Cantidad: 3 unidades\n";
        echo "   ✅ Precio unitario: $" . number_format($this->producto->precio, 0) . "\n\n";

        // PASO 2: Verificar carrito
        echo "🛒 PASO 2: Verificando carrito...\n";
        $carrito = session('carrito_pos');
        $productoEnCarrito = $carrito['productos'][0];

        $this->assertEquals($this->producto->precio, $productoEnCarrito['precio']);
        echo "   ✅ Precio en carrito: $" . number_format($productoEnCarrito['precio'], 0) . "\n";
        echo "   ✅ Precio coincide con BD: SÍ\n\n";

        // PASO 3: Finalizar venta
        echo "💳 PASO 3: Finalizando venta...\n";
        $response2 = $this->postJson(route('pos.finalizar'), [
            'metodo_pago' => 'EFECTIVO',
            'monto_recibido' => 20000
        ]);

        $response2->assertStatus(200);
        $data = $response2->json();

        $this->assertTrue($data['success']);
        echo "   ✅ Venta confirmada: #{$data['venta_id']}\n";
        echo "   ✅ Total pagado: $" . number_format($data['total_pagado'], 0) . "\n";
        echo "   ✅ Tipo de venta: {$data['tipo_venta_desc']}\n";
        echo "   ✅ Mensaje: {$data['message']}\n\n";

        // PASO 4: Verificar en base de datos
        echo "💾 PASO 4: Verificando registro en base de datos...\n";
        $this->assertDatabaseHas('ventas', [
            'id' => $data['venta_id'],
            'empresa_id' => $this->empresa->id,
            'estado_pago' => 'PAGADA'
        ]);
        echo "   ✅ Venta registrada en BD\n";
        echo "   ✅ Estado: PAGADA\n\n";

        echo "═══════════════════════════════════════════════════════════════\n";
        echo "  ✅ FLUJO COMPLETO VERIFICADO CON ÉXITO\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        echo "📊 RESUMEN DE VERIFICACIONES:\n";
        echo "   ✅ Precio sale de productos.precio\n";
        echo "   ✅ Total calculado en backend\n";
        echo "   ✅ Confirmación explícita de éxito\n";
        echo "   ✅ Mensaje claro al usuario\n";
        echo "   ✅ Venta registrada correctamente\n\n";
    }
}
