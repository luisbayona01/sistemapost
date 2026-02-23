<?php
use App\Models\User;
use App\Services\VentaService;
use Illuminate\Support\Facades\Auth;

$user = User::first();
Auth::login($user);

$ventaService = app(VentaService::class);

$data = [
    'metodo_pago' => 'EFECTIVO',
    'solicita_factura' => false, // Consumidor final
    'subtotal_confiteria' => 50000, // < 5 UVT
    'canal' => 'confiteria',
    'productos' => [
        [
            'producto_id' => 83,
            'cantidad' => 1,
            'precio' => 50000
        ]
    ]
];

try {
    echo "Iniciando venta POS de prueba (Bajo umbral)...\n";
    $venta = $ventaService->procesarVenta($data);
    echo "Venta #{$venta->id} creada exitosamente.\n";
    echo "Total: \${$venta->total}\n";
    echo "Verificando si se creó el documento fiscal...\n";

    sleep(2);

    $docFiscal = \App\Models\DocumentoFiscal::where('venta_id', $venta->id)->first();

    if ($docFiscal) {
        echo "DOCUMENTO FISCAL ENCONTRADO:\n";
        echo "Tipo: {$docFiscal->tipo_documento}\n";
        echo "Número: {$docFiscal->numero_completo}\n";
        echo "Estado: {$docFiscal->estado}\n";
    } else {
        echo "ERROR: No se encontró documento fiscal.\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
