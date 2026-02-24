<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Corrigiendo logs...\n";
$affected = DB::statement("
    UPDATE activity_logs al
    JOIN users u ON al.user_id = u.id
    SET al.empresa_id = u.empresa_id
    WHERE al.empresa_id IS NULL
");
echo "Logs actualizados.\n";

$huerfanos = DB::table('activity_logs')->whereNull('empresa_id')->count();
echo "Logs restantes sin empresa: $huerfanos\n";

if ($huerfanos > 0) {
    echo "Eliminando logs huérfanos sin usuario...\n";
    DB::table('activity_logs')->whereNull('empresa_id')->delete();
}
echo "PROCESO COMPLETADO\n";
