<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('name', 'Super Administrador')->first();
auth()->login($user);

$allRetail = App\Models\Producto::where('es_venta_retail', true)->get();
echo "Total Retail (raw): " . $allRetail->count() . "\n";
foreach ($allRetail as $p) {
    echo "- ID: {$p->id} | Nombre: {$p->nombre} | Empresa: {$p->empresa_id}\n";
}
