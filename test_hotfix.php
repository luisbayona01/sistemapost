<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;

$credentials = ['email' => 'prueba2@test.com', 'password' => 'password'];

if (Auth::attempt($credentials)) {
    echo "Login Successful\n";
    $user = Auth::user();

    echo "Checking roles...\n";
    try {
        $roles = $user->getRoleNames();
        echo "Roles count: " . count($roles) . "\n";
        foreach ($roles as $role) {
            echo "Role: $role\n";
        }
    } catch (\Exception $e) {
        echo "ERROR checking roles: " . $e->getMessage() . "\n";
    }

    echo "\nChecking a scoped model (Venta)...\n";
    $venta = \App\Models\Venta::first();
    if ($venta) {
        echo "Found Venta ID: " . $venta->id . " | Empresa ID: " . $venta->empresa_id . "\n";
    } else {
        echo "No Venta found for this tenant.\n";
    }

    echo "DONE\n";
} else {
    echo "Login FAILED\n";
}
