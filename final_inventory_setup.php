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
    echo "--- CONFIGURANDO INVENTARIO INTEGRADO (RECETAS Y VENTA DIRECTA) ---\n";
    $empresaId = 1;

    // 1. Helpers para Estructura
    function getCat($nombre)
    {
        $c = Caracteristica::firstOrCreate(['nombre' => $nombre], ['estado' => 1]);
        return Categoria::firstOrCreate(['caracteristica_id' => $c->id]);
    }

    function getPres($nombre, $sigla)
    {
        $c = Caracteristica::firstOrCreate(['nombre' => $nombre], ['estado' => 1]);
        return Presentacione::firstOrCreate(['caracteristica_id' => $c->id], ['sigla' => $sigla]);
    }

    $catConfiteria = getCat('Confitería');
    $presUnd = getPres('Unidad', 'UND');

    // 2. Definición de Insumos (Materia Prima + Productos Terminados)
    $insumosParaCrear = [
        // Materia Prima para Preparados
        ['n' => 'Maíz Pira Premium (kg)', 'u' => 'kg', 'c' => 8500, 's' => 50],
        ['n' => 'Aceite Vegetal (L)', 'u' => 'L', 'c' => 12500, 's' => 20],
        ['n' => 'Sal Refinada (kg)', 'u' => 'kg', 'c' => 2200, 's' => 5],
        ['n' => 'Jarabe Original (L)', 'u' => 'L', 'c' => 18000, 's' => 10],

        // Productos Terminados (Venta Directa)
        ['n' => 'Chocolatina Jet 12g', 'u' => 'UND', 'c' => 650, 's' => 100],
        ['n' => 'Gaseosa Coca-Cola Lata 330ml', 'u' => 'UND', 'c' => 2200, 's' => 48],
        ['n' => 'Agua Mineral 500ml', 'u' => 'UND', 'c' => 1200, 's' => 60],
        ['n' => 'Barra Cereal Mix', 'u' => 'UND', 'c' => 1800, 's' => 30],

        // Packaging
        ['n' => 'Caja Popcorn L', 'u' => 'UND', 'c' => 1100, 's' => 200],
        ['n' => 'Vaso 22oz + Tapa', 'u' => 'UND', 'c' => 950, 's' => 500],
    ];

    $insumosDB = [];
    foreach ($insumosParaCrear as $i) {
        $insumo = Insumo::updateOrCreate(
            ['nombre' => $i['n'], 'empresa_id' => $empresaId],
            [
                'codigo' => 'INS-' . strtoupper(substr(md5($i['n']), 0, 8)),
                'unidad_medida' => $i['u'],
                'costo_unitario' => $i['c'],
                'stock_actual' => $i['s'],
                'stock_minimo' => 5
            ]
        );
        $insumosDB[$i['n']] = $insumo;

        // Crear Lote para FIFO
        InsumoLote::updateOrCreate(
            ['insumo_id' => $insumo->id, 'numero_lote' => 'LOTE-INIT-2026'],
            [
                'cantidad_inicial' => $i['s'],
                'cantidad_actual' => $i['s'],
                'costo_unitario' => $i['c'],
                'fecha_vencimiento' => now()->addYear()
            ]
        );
    }
    echo "✔ Insumos y Stock Inicial (FIFO) cargados.\n";

    // 3. Configuración de Productos y Recetas
    $productosConfig = [
        // --- PRODUCTOS PREPARADOS (Receta Múltiple) ---
        [
            'nombre' => 'Combo Popcorn Grande',
            'pv' => 25000,
            'receta' => [
                ['n' => 'Maíz Pira Premium (kg)', 'q' => 0.250, 'm' => 12],
                ['n' => 'Aceite Vegetal (L)', 'q' => 0.040],
                ['n' => 'Sal Refinada (kg)', 'q' => 0.010],
                ['n' => 'Caja Popcorn L', 'q' => 1],
            ]
        ],
        // --- PRODUCTOS DIRECTOS (Receta 1-a-1) ---
        [
            'nombre' => 'Chocolatina Jet',
            'pv' => 2500,
            'receta' => [['n' => 'Chocolatina Jet 12g', 'q' => 1]]
        ],
        [
            'nombre' => 'Gaseosa en Lata 330ml',
            'pv' => 5500,
            'receta' => [['n' => 'Gaseosa Coca-Cola Lata 330ml', 'q' => 1]]
        ],
        [
            'nombre' => 'Agua Botella 500ml',
            'pv' => 4500,
            'receta' => [['n' => 'Agua Mineral 500ml', 'q' => 1]]
        ],
        [
            'nombre' => 'Barra de Cereal Nutritiva',
            'pv' => 4000,
            'receta' => [['n' => 'Barra Cereal Mix', 'q' => 1]]
        ]
    ];

    foreach ($productosConfig as $pc) {
        $producto = Producto::updateOrCreate(
            ['nombre' => $pc['nombre'], 'empresa_id' => $empresaId],
            [
                'precio' => $pc['pv'],
                'categoria_id' => $catConfiteria->id,
                'presentacione_id' => $presUnd->id,
                'estado' => 1,
                'codigo' => 'PROD-' . strtoupper(substr(md5($pc['nombre']), 0, 8))
            ]
        );

        // Limpiar y recrear receta
        DB::table('recetas')->where('producto_id', $producto->id)->delete();
        foreach ($pc['receta'] as $ri) {
            $insumo = $insumosDB[$ri['n']];
            Receta::create([
                'producto_id' => $producto->id,
                'insumo_id' => $insumo->id,
                'cantidad' => $ri['q'],
                'unidad_medida' => $insumo->unidad_medida,
                'merma_esperada' => $ri['m'] ?? 0
            ]);
        }
    }

    echo "✔ Productos y Recetas (Unificadas) vinculados correctamente.\n";
    echo "\nRESUMEN PARA AUDITORÍA:\n";
    echo "- Todos los productos (incluyendo chocolatinas) ahora descuentan de Insumos.\n";
    echo "- Se habilitó la lógica FIFO: compras de diferentes precios se trackean solas.\n";
    echo "- La chocolatina ahora se compra como INSUMO y se vende como PRODUCTO (relación 1:1).\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
