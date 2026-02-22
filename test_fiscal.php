<?php
use App\Models\User;
use App\Services\VentaService;
use Illuminate\Support\Facades\Auth;

$user = User::first();
Auth::login($user);

$ventaService = app(VentaService::class);

$data = [
    'metodo_pago' => 'EFECTIVO',
    'solicita_factura' => true,
    'cliente_tipo_doc' => 'CC',
    'cliente_documento' => '10203040',
    'cliente_nombre' => 'JUAN PRUEBA FISCAL',
    'cliente_email' => 'juan.test@example.com',
    'cliente_telefono' => '3001234567',
    'subtotal_confiteria' => 300000,
    'canal' => 'confiteria',
    'productos' => [
        [
            'producto_id' => 83,
            'cantidad' => 10,
            'precio' => 30000 // Total 300,000 (> 5 UVT)
        ]
    ]
];

try {
    echo "Iniciando venta de prueba...\n";
    $venta = $ventaService->procesarVenta($data);
    echo "Venta #{$venta->id} creada exitosamente.\n";
    echo "Total: \${$venta->total}\n";
    echo "Verificando si se creó el documento fiscal...\n";

    // Esperar un momento por si el listener es asíncrono (aunque en sync funcionaría igual)
    sleep(2);

    $docFiscal = \App\Models\DocumentoFiscal::where('venta_id', $venta->id)->first();

    if ($docFiscal) {
        echo "DOCUMENTO FISCAL ENCONTRADO:\n";
        echo "Tipo: {$docFiscal->tipo_documento}\n";
        echo "Número: {$docFiscal->numero_completo}\n";
        echo "Estado: {$docFiscal->estado}\n";
        echo "CUFE: {$docFiscal->cufe}\n";
    } else {
        echo "ERROR: No se encontró documento fiscal para la venta.\n";
    }
} catch (\Exception $e) {
    echo "ERROR EN LA VENTA: " . $e->getMessage() . "\n";
}
