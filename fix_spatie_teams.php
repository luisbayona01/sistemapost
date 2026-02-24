<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$app->make('db'); // boot DB

// 1. Actualizar model_has_roles
$sql1 = "
    UPDATE model_has_roles mr
    INNER JOIN users u ON u.id = mr.model_id AND mr.model_type = 'App\\\\Models\\\\User'
    SET mr.empresa_id = u.empresa_id
    WHERE mr.empresa_id IS NULL AND u.empresa_id IS NOT NULL
";
$r1 = DB::statement($sql1);
$pending1 = DB::table('model_has_roles')->whereNull('empresa_id')->count();
echo "[model_has_roles] OK - Filas restantes NULL: {$pending1}\n";

// 2. Actualizar model_has_permissions
$sql2 = "
    UPDATE model_has_permissions mp
    INNER JOIN users u ON u.id = mp.model_id AND mp.model_type = 'App\\\\Models\\\\User'
    SET mp.empresa_id = u.empresa_id
    WHERE mp.empresa_id IS NULL AND u.empresa_id IS NOT NULL
";
$r2 = DB::statement($sql2);
$pending2 = DB::table('model_has_permissions')->whereNull('empresa_id')->count();
echo "[model_has_permissions] OK - Filas restantes NULL: {$pending2}\n";

echo "DONE\n";
