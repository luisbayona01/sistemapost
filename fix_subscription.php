<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

try {
    echo "--- AJUSTANDO EMPRESA Y SUSCRIPCIÓN ---\n";

    // 1. Forzar empresa 1 a estar activa y con suscripción válida
    $empresa = Empresa::find(1);
    if ($empresa) {
        $empresa->update([
            'estado' => 'activa',
            'estado_suscripcion' => 'active',
            'nombre' => 'Cinema Paraíso'
        ]);
        echo "✔ Empresa 1 actualizada a Activa/Suscripción Activa.\n";
    } else {
        echo "⚠ Empresa 1 no encontrada.\n";
    }

    // 2. Verificar que el usuario admin esté apuntando a la empresa 1
    $user = User::where('username', 'admin')->first();
    if ($user) {
        $user->update([
            'empresa_id' => 1,
            'estado' => 1
        ]);
        echo "✔ Usuario 'admin' verificado y vinculado a Empresa 1.\n";
    }

    echo "\n--- PROCESO FINALIZADO ---\n";
    echo "Intenta loguearte nuevamente con:\n";
    echo "Usuario: admin\n";
    echo "Clave: 12345678\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
