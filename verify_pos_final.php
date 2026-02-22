<?php

use App\Models\Categoria;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICACIÓN DE POS ===" . PHP_EOL;

// Simular consulta del controlador
$categorias = Categoria::with([
    'caracteristica',
    'productos' => function ($query) {
        $query->where('es_venta_retail', true)
            ->where('nombre', '!=', 'Ticket Cine Genérico')
            ->where('nombre', 'not like', 'TICKET_CINEMA%');
    }
])
    ->whereHas('productos', function ($query) {
        $query->where('es_venta_retail', true)
            ->where('nombre', '!=', 'Ticket Cine Genérico');
    })
    ->get();

echo "Categorías cargadas: " . $categorias->count() . PHP_EOL;

foreach ($categorias as $cat) {
    echo "Categoria: '{$cat->nombre}' (ID: {$cat->id})" . PHP_EOL;
    echo "  Productos Retail: " . $cat->productos->count() . PHP_EOL;
    foreach ($cat->productos as $p) {
        echo "   - {$p->nombre}" . PHP_EOL;
    }
    echo PHP_EOL;
}
