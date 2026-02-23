<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empresa;
use Spatie\Permission\Models\Role;

class GuestAccessSeeder extends Seeder
{
    public function run()
    {
        $empresa = Empresa::first();
        if (!$empresa) {
            $empresa = Empresa::create([
                'nombre' => 'CinePost Demo',
                'estado' => 'activa',
                'ruc' => '1234567890',
                'moneda_id' => 1,
                'direccion' => 'Calle Falsa 123',
                'correo' => 'test@cinepost.com',
                'telefono' => '123456789'
            ]);
        }

        $user = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin Lead',
                'password' => bcrypt('password123'),
                'empresa_id' => $empresa->id,
                'estado' => 1
            ]
        );

        $rol = Role::where('name', 'administrador')->first();
        if ($rol) {
            $user->assignRole($rol);
        }
    }
}
