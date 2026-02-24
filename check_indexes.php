<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$tablas = ['productos', 'users', 'clientes', 'peliculas', 'salas'];

foreach ($tablas as $tabla) {
    echo "\n--- Análisis de tabla: $tabla ---\n";
    $indexes = DB::select("SHOW INDEX FROM $tabla WHERE Non_unique = 0 AND Key_name != 'PRIMARY'");

    foreach ($indexes as $idx) {
        $keyName = $idx->Key_name;
        $column = $idx->Column_name;

        // Verificar si empresa_id es parte del índice
        $parts = DB::select("SHOW INDEX FROM $tabla WHERE Key_name = '$keyName'");
        $hasEmpresa = false;
        foreach ($parts as $p) {
            if ($p->Column_name == 'empresa_id')
                $hasEmpresa = true;
        }

        if (!$hasEmpresa) {
            echo "⚠️  RIESGO: Índice único '$keyName' en columna '$column' NO incluye empresa_id.\n";
        } else {
            echo "✅ OK: Índice único '$keyName' incluye empresa_id.\n";
        }
    }
}
