<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Insumo;
use App\Models\InsumoLote;
use App\Models\Producto;
use App\Models\Receta;
use App\Models\Empresa;
use App\Models\Categoria;
use App\Models\Presentacione;
use App\Models\Caracteristica;
use Illuminate\Support\Facades\DB;

try {
    echo "--- CREANDO PRODUCTOS E INSUMOS DE DEMO ---\n";
    $empresaId = 1;

    // 1. Helper para crear Categorías y Presentaciones (vía Característica)
    function findOrCreateCat($nombre, $empId)
    {
        $c = Caracteristica::firstOrCreate(['nombre' => $nombre], ['estado' => 1]);
        return Categoria::firstOrCreate(['caracteristica_id' => $c->id]);
    }

    function findOrCreatePres($nombre, $sigla, $empId)
    {
        $c = Caracteristica::firstOrCreate(['nombre' => $nombre], ['estado' => 1]);
        return Presentacione::firstOrCreate(['caracteristica_id' => $c->id], ['sigla' => $sigla]);
    }

    $catBebidas = findOrCreateCat('Bebidas', $empresaId);
    $catSnacks = findOrCreateCat('Snacks', $empresaId);
    $presUnidad = findOrCreatePres('Unidad', 'UND', $empresaId);

    // 2. Crear Insumos
    $insumosData = [
        ['nombre' => 'Jarabe Coca-Cola (L)', 'unidad' => 'L', 'costo' => 15000, 'min' => 5],
        ['nombre' => 'Maíz Pira (kg)', 'unidad' => 'kg', 'costo' => 8000, 'min' => 10],
        ['nombre' => 'Aceite Vegetal (L)', 'unidad' => 'L', 'costo' => 12000, 'min' => 5],
        ['nombre' => 'Sal Refinada (kg)', 'unidad' => 'kg', 'costo' => 2000, 'min' => 2],
        ['nombre' => 'Agua Mineral 500ml Insumo', 'unidad' => 'UND', 'costo' => 1500, 'min' => 20],
        ['nombre' => 'Vaso Cine 22oz', 'unidad' => 'UND', 'costo' => 800, 'min' => 50],
        ['nombre' => 'Empaque Crispetas L', 'unidad' => 'UND', 'costo' => 1200, 'min' => 50],
    ];

    $insumosMap = [];
    foreach ($insumosData as $data) {
        $insumo = Insumo::updateOrCreate(
            ['nombre' => $data['nombre'], 'empresa_id' => $empresaId],
            [
                'codigo' => 'INS-' . strtoupper(substr(md5($data['nombre']), 0, 6)),
                'unidad_medida' => $data['unidad'],
                'costo_unitario' => $data['costo'],
                'stock_actual' => 100,
                'stock_minimo' => $data['min']
            ]
        );
        $insumosMap[$data['nombre']] = $insumo;

        InsumoLote::updateOrCreate(
            ['insumo_id' => $insumo->id, 'numero_lote' => 'LOTE-DEMO-001'],
            [
                'cantidad_inicial' => 100,
                'cantidad_actual' => 100,
                'costo_unitario' => $data['costo'],
                'fecha_vencimiento' => now()->addMonths(6)
            ]
        );
    }
    echo "✔ Insumos y lotes configurados.\n";

    // 3. Crear Productos
    $productosData = [
        [
            'nombre' => 'Coca-Cola 22oz',
            'precio' => 12000,
            'cat' => $catBebidas->id,
            'receta' => [
                ['name' => 'Jarabe Coca-Cola (L)', 'cant' => 0.150],
                ['name' => 'Vaso Cine 22oz', 'cant' => 1]
            ]
        ],
        [
            'nombre' => 'Crispetas Saladas L',
            'precio' => 18000,
            'cat' => $catSnacks->id,
            'receta' => [
                ['name' => 'Maíz Pira (kg)', 'cant' => 0.200, 'merma' => 10],
                ['name' => 'Aceite Vegetal (L)', 'cant' => 0.050],
                ['name' => 'Sal Refinada (kg)', 'cant' => 0.010],
                ['name' => 'Empaque Crispetas L', 'cant' => 1]
            ]
        ],
        [
            'nombre' => 'Botella Agua 500ml',
            'precio' => 6000,
            'cat' => $catBebidas->id,
            'receta' => [
                ['name' => 'Agua Mineral 500ml Insumo', 'cant' => 1]
            ]
        ]
    ];

    foreach ($productosData as $pData) {
        $producto = Producto::updateOrCreate(
            ['nombre' => $pData['nombre'], 'empresa_id' => $empresaId],
            [
                'precio' => $pData['precio'],
                'categoria_id' => $pData['cat'],
                'presentacione_id' => $presUnidad->id,
                'estado' => 1
            ]
        );

        DB::table('recetas')->where('producto_id', $producto->id)->delete();
        foreach ($pData['receta'] as $rItem) {
            $insumo = $insumosMap[$rItem['name']];
            Receta::create([
                'producto_id' => $producto->id,
                'insumo_id' => $insumo->id,
                'cantidad' => $rItem['cant'],
                'unidad_medida' => $insumo->unidad_medida,
                'merma_esperada' => $rItem['merma'] ?? 0
            ]);
        }
    }

    echo "✔ Productos de venta y sus recetas creados correctamente.\n";
    echo "--- LISTO PARA AUDITORÍA DE INVENTARIO ---\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
