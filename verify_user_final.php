<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

try {
    $search = ['admin', 'admin@gmail.com'];
    foreach ($search as $term) {
        $user = User::where('username', $term)->orWhere('email', $term)->first();
        if ($user) {
            echo "FOUND: Username[{$user->username}] Email[{$user->email}] Estado[{$user->estado}] EmpresaID[{$user->empresa_id}]\n";
        } else {
            echo "NOT FOUND: {$term}\n";
        }
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
