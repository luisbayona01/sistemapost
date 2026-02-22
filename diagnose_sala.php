<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- QUERY 1 RESULT ---\n";
try {
    $res1 = Illuminate\Support\Facades\DB::select('SELECT id, nombre, capacidad, empresa_id FROM salas ORDER BY id');
    print_r($res1);
} catch (\Exception $e) {
    echo "Error Query 1: " . $e->getMessage() . "\n";
}

echo "\n--- QUERY 2 RESULT ---\n";
try {
    $res2 = Illuminate\Support\Facades\DB::select('SELECT f.id as funcion_id, f.fecha_hora, s.id as sala_id, s.nombre as sala, s.capacidad, (SELECT COUNT(*) FROM funcion_asientos fa WHERE fa.funcion_id = f.id) as asientos_generados FROM funciones f JOIN salas s ON f.sala_id = s.id WHERE s.id = 2 ORDER BY f.fecha_hora DESC LIMIT 10');
    print_r($res2);
} catch (\Exception $e) {
    echo "Error Query 2: " . $e->getMessage() . "\n";
}
