<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Producto;
use App\Models\Pelicula;
use App\Models\Insumo;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacione;
use App\Models\Inventario;
use App\Models\Kardex;
use App\Enums\TipoTransaccionEnum;

class SimulacionPOSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧹 Limpiando datos antiguos...');
        $this->limpiarDatos();

        $this->command->info('📦 Creando categorías...');
        $categorias = $this->crearCategorias();

        $this->command->info('🏷️ Creando marcas y presentaciones...');
        $marca = $this->crearMarcaYPresentacion();

        $this->command->info('🥫 Creando insumos...');
        $insumos = $this->crearInsumos();

        $this->command->info('🍕 Creando productos de confitería...');
        $productos = $this->crearProductos($categorias, $marca);

        $this->command->info('🧪 Creando recetas (asociando insumos a productos)...');
        $this->crearRecetas($productos, $insumos);

        $this->command->info('📊 Creando inventario inicial...');
        $this->crearInventarioInicial($productos, $insumos);

        $this->command->info('✅ Simulación POS completada exitosamente!');
    }

    private function limpiarDatos(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Limpiar ventas y movimientos para reiniciar reportes
        if (Schema::hasTable('producto_venta'))
            DB::table('producto_venta')->truncate();
        if (Schema::hasTable('ventas'))
            DB::table('ventas')->truncate();
        if (Schema::hasTable('movimientos_caja'))
            DB::table('movimientos_caja')->truncate();
        if (Schema::hasTable('pagos'))
            DB::table('pagos')->truncate();
        if (Schema::hasTable('payment_transactions'))
            DB::table('payment_transactions')->truncate();

        // Limpiar solo productos retail (mantener el ticket de cine)
        $productosRetail = Producto::where('es_venta_retail', true)->pluck('id');

        // Eliminar relaciones
        if (Schema::hasTable('recetas'))
            DB::table('recetas')->whereIn('producto_id', $productosRetail)->delete();
        if (Schema::hasTable('kardex'))
            DB::table('kardex')->whereIn('producto_id', $productosRetail)->delete();
        if (Schema::hasTable('inventario'))
            DB::table('inventario')->whereIn('producto_id', $productosRetail)->delete();

        // Eliminar productos retail
        Producto::where('es_venta_retail', true)->delete();

        // Limpiar películas
        DB::table('funcion_asientos')->delete();
        DB::table('funciones')->delete();
        Pelicula::truncate();

        // Limpiar insumos
        DB::table('kardex')->whereNotNull('insumo_id')->delete();
        // DB::table('lotes')->delete(); // Tabla no existe en el esquema actual
        Insumo::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function crearCategorias(): array
    {
        $empresaId = 1;
        $categorias = [];

        $categoriasData = [
            ['nombre' => 'Comida', 'descripcion' => 'Alimentos preparados'],
            ['nombre' => 'Bebidas', 'descripcion' => 'Bebidas frías y calientes'],
            ['nombre' => 'Tragos', 'descripcion' => 'Bebidas alcohólicas'],
        ];

        foreach ($categoriasData as $data) {
            $caracteristica = DB::table('caracteristicas')->insertGetId([
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $categoria = Categoria::create([
                'caracteristica_id' => $caracteristica,
                'empresa_id' => $empresaId,
            ]);

            $categorias[$data['nombre']] = $categoria;
        }

        return $categorias;
    }

    private function crearMarcaYPresentacion(): array
    {
        $empresaId = 1;

        // Marca genérica
        $caracteristicaMarca = DB::table('caracteristicas')->insertGetId([
            'nombre' => 'Casa',
            'descripcion' => 'Productos de la casa',
            'estado' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $marca = Marca::create([
            'caracteristica_id' => $caracteristicaMarca,
            'empresa_id' => $empresaId,
        ]);

        // Presentación genérica
        $caracteristicaPres = DB::table('caracteristicas')->insertGetId([
            'nombre' => 'Unidad',
            'descripcion' => 'Unidad individual',
            'estado' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $presentacion = Presentacione::create([
            'caracteristica_id' => $caracteristicaPres,
            'sigla' => 'UND',
            'empresa_id' => $empresaId,
        ]);

        return [
            'marca' => $marca,
            'presentacion' => $presentacion,
        ];
    }

    private function crearInsumos(): array
    {
        $empresaId = 1;
        $insumos = [];

        $insumosData = [
            // Para perros calientes
            ['nombre' => 'Pan para perro', 'unidad' => 'und', 'costo' => 1500],
            ['nombre' => 'Salchicha', 'unidad' => 'und', 'costo' => 2000],
            ['nombre' => 'Salsas y aderezos', 'unidad' => 'g', 'costo' => 15], // $15/g ($15,000/kg)

            // Para pizzas
            ['nombre' => 'Masa de pizza', 'unidad' => 'und', 'costo' => 3000],
            ['nombre' => 'Queso mozzarella', 'unidad' => 'g', 'costo' => 28], // $28/g ($28,000/kg)
            ['nombre' => 'Jamón', 'unidad' => 'g', 'costo' => 25], // $25/g ($25,000/kg)
            ['nombre' => 'Salsa de tomate', 'unidad' => 'g', 'costo' => 12], // $12/g ($12,000/kg)

            // Bebidas
            ['nombre' => 'Gaseosa embotellada', 'unidad' => 'und', 'costo' => 2000],
            ['nombre' => 'Agua embotellada', 'unidad' => 'und', 'costo' => 1500],
            ['nombre' => 'Cerveza botella', 'unidad' => 'und', 'costo' => 3500],
            ['nombre' => 'Vino tinto', 'unidad' => 'ml', 'costo' => 60], // $45,000 / 750ml
            ['nombre' => 'Vino blanco', 'unidad' => 'ml', 'costo' => 60],
            ['nombre' => 'Licores para cocteles', 'unidad' => 'ml', 'costo' => 80], // $60,000 / 750ml

            // Snacks
            ['nombre' => 'Maíz para crispetas', 'unidad' => 'g', 'costo' => 10], // $10/g ($10,000/kg)
            ['nombre' => 'Aceite y sal', 'unidad' => 'g', 'costo' => 8],
            ['nombre' => 'Mezcla para brownie', 'unidad' => 'g', 'costo' => 20], // $20/g ($20,000/kg)
        ];

        foreach ($insumosData as $data) {
            $insumo = Insumo::create([
                'nombre' => $data['nombre'],
                'unidad_medida' => $data['unidad'],
                'costo_unitario' => $data['costo'],
                'stock_actual' => 500, // Stock inicial generoso
                'stock_minimo' => 50,
                'empresa_id' => $empresaId,
            ]);

            $insumos[$data['nombre']] = $insumo;

            // Crear Lote Inicial para InventoryService logic
            \App\Models\InsumoLote::create([
                'insumo_id' => $insumo->id,
                'numero_lote' => 'LOTE-SIM-' . $insumo->id,
                'cantidad_inicial' => 500,
                'cantidad_actual' => 500,
                'costo_unitario' => $data['costo'],
                'fecha_vencimiento' => now()->addYear(),
            ]);

            // Registrar en Kardex
            $kardex = new Kardex();
            $kardex->crearRegistro([
                'insumo_id' => $insumo->id,
                'cantidad' => 500,
                'tipo_movimiento' => 'entrada',
                'motivo' => 'Stock inicial de simulación',
                'empresa_id' => $empresaId,
                'costo_unitario' => $data['costo'],
            ], TipoTransaccionEnum::Apertura);
        }

        return $insumos;
    }

    private function crearProductos(array $categorias, array $marca): array
    {
        $empresaId = 1;
        $productos = [];

        $productosData = [
            // Comida
            ['nombre' => 'Perro caliente', 'precio' => 35000, 'categoria' => 'Comida', 'costo' => 10500],
            ['nombre' => 'Pizza margarita', 'precio' => 34000, 'categoria' => 'Comida', 'costo' => 10200],
            ['nombre' => 'Pizza de jamón', 'precio' => 36000, 'categoria' => 'Comida', 'costo' => 10800],
            ['nombre' => 'Brownie', 'precio' => 16000, 'categoria' => 'Comida', 'costo' => 4800],
            ['nombre' => 'Crispetas', 'precio' => 14000, 'categoria' => 'Comida', 'costo' => 4200],

            // Bebidas
            ['nombre' => 'Gaseosa o agua', 'precio' => 8500, 'categoria' => 'Bebidas', 'costo' => 2550],
            ['nombre' => 'Cerveza', 'precio' => 14000, 'categoria' => 'Bebidas', 'costo' => 4200],

            // Tragos
            ['nombre' => 'Copa de vino tinto', 'precio' => 35000, 'categoria' => 'Tragos', 'costo' => 10500],
            ['nombre' => 'Copa de vino blanco', 'precio' => 35000, 'categoria' => 'Tragos', 'costo' => 10500],
            ['nombre' => 'Coctel', 'precio' => 35000, 'categoria' => 'Tragos', 'costo' => 10500],
        ];

        foreach ($productosData as $data) {
            $producto = Producto::create([
                'nombre' => $data['nombre'],
                'precio' => $data['precio'],
                'categoria_id' => $categorias[$data['categoria']]->id,
                'marca_id' => $marca['marca']->id,
                'presentacione_id' => $marca['presentacion']->id,
                'empresa_id' => $empresaId,
                'es_venta_retail' => true,
                'descripcion' => 'Producto de simulación POS',
            ]);

            $productos[$data['nombre']] = [
                'producto' => $producto,
                'costo_objetivo' => $data['costo'],
            ];
        }

        return $productos;
    }

    private function crearRecetas(array $productos, array $insumos): void
    {
        $recetas = [
            'Perro caliente' => [
                ['insumo' => 'Pan para perro', 'cantidad' => 1],
                ['insumo' => 'Salchicha', 'cantidad' => 1],
                ['insumo' => 'Salsas y aderezos', 'cantidad' => 50],
            ],
            'Pizza margarita' => [
                ['insumo' => 'Masa de pizza', 'cantidad' => 1],
                ['insumo' => 'Queso mozzarella', 'cantidad' => 150],
                ['insumo' => 'Salsa de tomate', 'cantidad' => 100],
            ],
            'Pizza de jamón' => [
                ['insumo' => 'Masa de pizza', 'cantidad' => 1],
                ['insumo' => 'Queso mozzarella', 'cantidad' => 150],
                ['insumo' => 'Jamón', 'cantidad' => 100],
                ['insumo' => 'Salsa de tomate', 'cantidad' => 100],
            ],
            'Brownie' => [
                ['insumo' => 'Mezcla para brownie', 'cantidad' => 150],
            ],
            'Crispetas' => [
                ['insumo' => 'Maíz para crispetas', 'cantidad' => 100],
                ['insumo' => 'Aceite y sal', 'cantidad' => 20],
            ],
            'Gaseosa o agua' => [
                ['insumo' => 'Gaseosa embotellada', 'cantidad' => 1],
            ],
            'Cerveza' => [
                ['insumo' => 'Cerveza botella', 'cantidad' => 1],
            ],
            'Copa de vino tinto' => [
                ['insumo' => 'Vino tinto', 'cantidad' => 150],
            ],
            'Copa de vino blanco' => [
                ['insumo' => 'Vino blanco', 'cantidad' => 150],
            ],
            'Coctel' => [
                ['insumo' => 'Licores para cocteles', 'cantidad' => 60],
            ],
        ];

        foreach ($recetas as $nombreProducto => $ingredientes) {
            $producto = $productos[$nombreProducto]['producto'];

            foreach ($ingredientes as $ingrediente) {
                $insumo = $insumos[$ingrediente['insumo']];

                DB::table('recetas')->insert([
                    'producto_id' => $producto->id,
                    'insumo_id' => $insumo->id,
                    'cantidad' => $ingrediente['cantidad'],
                    'unidad_medida' => $insumo->unidad_medida,
                    'merma_esperada' => 5, // 5% de merma estándar
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Recalcular costos del producto
            $producto->refresh();
            $producto->calcularRentabilidad();
        }
    }

    private function crearInventarioInicial(array $productos, array $insumos): void
    {
        $empresaId = 1;
        $ubicacionId = 1; // Asumiendo ubicación por defecto

        foreach ($productos as $data) {
            $producto = $data['producto'];
            $stockInicial = 50;

            // Crear inventario
            Inventario::create([
                'producto_id' => $producto->id,
                'cantidad' => $stockInicial,
                'cantidad_minima' => 10,
                'ubicacione_id' => $ubicacionId,
                'empresa_id' => $empresaId,
            ]);

            // Registrar en Kardex
            $kardex = new Kardex();
            $kardex->crearRegistro([
                'producto_id' => $producto->id,
                'cantidad' => $stockInicial,
                'tipo_movimiento' => 'entrada',
                'motivo' => 'Stock inicial de simulación',
                'empresa_id' => $empresaId,
                'costo_unitario' => $producto->costo_total_unitario ?? 0,
            ], TipoTransaccionEnum::Apertura);
        }
    }
}
