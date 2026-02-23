<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;

$credentials = ['email' => 'invitado@gmail.com', 'password' => '12345678'];

if (Auth::attempt($credentials)) {
    echo "Login Successful for invitado@gmail.com\n";
} else {
    echo "Login FAILED for invitado@gmail.com\n";
    $user = \App\Models\User::where('email', 'invitado@gmail.com')->first();
    if ($user) {
        echo "User exists.\n";
        echo "Hash matches: " . (password_verify('12345678', $user->password) ? 'YES' : 'NO') . "\n";
    } else {
        echo "User NOT FOUND.\n";
    }
}
