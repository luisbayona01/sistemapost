<?php

namespace Database\Seeders;

use App\Models\Caracteristica;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Empresa;
use App\Models\Moneda;
use App\Models\Persona;
use App\Models\Presentacione;
use App\Models\Producto;
use App\Models\StripeConfig;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Crea datos demo realistas para CinemaPOS
     */
    public function run(): void
    {
        // 1. Crear/obtener Moneda USD
        $moneda = Moneda::firstOrCreate(
            ['estandar_iso' => 'USD'],
            ['nombre_completo' => 'Dólar Estadounidense', 'simbolo' => '$']
        );

        // 2. Crear Empresa "Cinema Fénix"
        $empresa = Empresa::firstOrCreate(
            ['ruc' => '20123456789'],
            [
                'nombre' => 'Cinema Fénix',
                'propietario' => 'Admin Cinema Fénix',
                'porcentaje_impuesto' => 18,
                'abreviatura_impuesto' => 'IGV',
                'direccion' => 'Av. Principal 123, Piso 3',
                'telefono' => '+1234567890',
                'correo' => 'info@cinefenix.local',
                'ubicacion' => 'Lima',
                'moneda_id' => $moneda->id,
            ]
        );

        // 3. Crear Rol "Admin" (si no existe)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // 4. Crear Usuario Admin
        if (!User::where('email', 'admin@cinefenix.local')->exists()) {
            $admin = User::create([
                'empresa_id' => $empresa->id,
                'name' => 'Admin Cinema Fénix',
                'email' => 'admin@cinefenix.local',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            if (!$admin->hasRole('admin')) {
                $admin->assignRole('admin');
            }
        }

        // 5. Crear Configuración Stripe (TEST KEYS)
        StripeConfig::firstOrCreate(
            ['empresa_id' => $empresa->id],
            [
                'public_key' => 'pk_test_51234567890abcdefghijklmnopqrstuvwxyz',
                'secret_key' => 'sk_test_0987654321zyxwvutsrqponmlkjihgfedcba',
                'webhook_secret' => 'whsec_test_abcdefghijklmnopqrstuvwxyz1234567890',
                'test_mode' => true,
                'enabled' => true,
            ]
        );

        // 6. Crear Documento tipo si no existe
        $tipoDoc = Documento::firstOrCreate(['nombre' => 'DNI']);

        // 7. Crear Clientes de Demostración
        $clientes_data = [
            ['nombre' => 'Juan Pérez García', 'numero' => '12345678'],
            ['nombre' => 'María López Rodríguez', 'numero' => '87654321'],
            ['nombre' => 'Carlos Martínez López', 'numero' => '11223344'],
        ];

        foreach ($clientes_data as $cliente_data) {
            if (!Persona::where('numero_documento', $cliente_data['numero'])->exists()) {
                $persona = Persona::create([
                    'razon_social' => $cliente_data['nombre'],
                    'numero_documento' => $cliente_data['numero'],
                    'documento_id' => $tipoDoc->id,
                ]);

                Cliente::create([
                    'persona_id' => $persona->id,
                    'empresa_id' => $empresa->id,
                ]);
            }
        }

        // 8. Crear Presentaciones de Demostración
        $presentaciones = [
            'UND' => 'Unidad',
            'BOL' => 'Bolsa',
            'BOX' => 'Caja',
        ];

        foreach ($presentaciones as $sigla => $nombre) {
            $caracteristica = Caracteristica::firstOrCreate(
                ['nombre' => $nombre],
                ['descripcion' => "Característica: $nombre", 'estado' => 1]
            );

            Presentacione::firstOrCreate(
                ['sigla' => $sigla],
                ['caracteristica_id' => $caracteristica->id]
            );
        }

        // 9. Crear Característica para Categoría y obtener categoría
        $caracteristicaCategoria = Caracteristica::firstOrCreate(
            ['nombre' => 'Concesión'],
            ['descripcion' => 'Característica para categoría de concesión', 'estado' => 1]
        );

        $categoria = Categoria::firstOrCreate(
            ['caracteristica_id' => $caracteristicaCategoria->id],
            []
        );

        // 10. Crear Productos de Demostración
        $und = Presentacione::where('sigla', 'UND')->first();

        $productos_data = [
            ['nombre' => 'Palomitas Medianas', 'codigo' => 'PAL-001', 'precio' => 5.00],
            ['nombre' => 'Gaseosa Pequeña', 'codigo' => 'GAS-001', 'precio' => 3.00],
            ['nombre' => 'Candy Surtido', 'codigo' => 'CAN-001', 'precio' => 4.50],
        ];

        foreach ($productos_data as $prod_data) {
            Producto::firstOrCreate(
                ['codigo' => $prod_data['codigo']],
                [
                    'nombre' => $prod_data['nombre'],
                    'precio' => $prod_data['precio'],
                    'presentacione_id' => $und->id,
                    'categoria_id' => $categoria->id,
                    'empresa_id' => $empresa->id,
                    'estado' => 1,
                    'descripcion' => 'Producto de demo',
                ]
            );
        }

        echo "✅ Demo seeder completado!\n";
        echo "   Empresa: Cinema Fénix\n";
        echo "   Admin: admin@cinefenix.local / password123\n";
        echo "   Stripe: MODO TEST habilitado\n";
        echo "   Clientes: 3 (Juan, María, Carlos)\n";
        echo "   Productos: 3 (Palomitas, Gaseosa, Candy)\n";
    }
}
