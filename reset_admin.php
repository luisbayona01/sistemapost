<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;

$empresa = Empresa::where('id', 1)->first() ?? Empresa::first();

$user = User::updateOrCreate(
    ['email' => 'admin@gmail.com'],
    [
        'name' => 'Admin Cinema',
        'password' => Hash::make('12345678'),
        'empresa_id' => $empresa ? $empresa->id : 1,
        'estado' => 1
    ]
);

$user->assignRole('administrador');

echo "User admin@gmail.com has been RESET with password: 12345678\n";
