<?php

namespace App\Services\Fiscal;

use App\Interfaces\FiscalProviderInterface;
use App\Models\ConfiguracionNumeracion;
use App\Models\DocumentoFiscal;
use App\Models\DocumentoFiscalLinea;
use App\Models\Venta;
use App\Services\ContingenciaFiscalService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmisionFiscalService
{
    protected $provider;
    protected $contingencia;

    public function __construct(FiscalProviderInterface $provider, ContingenciaFiscalService $contingencia)
    {
        $this->provider = $provider;
        $this->contingencia = $contingencia;
    }

    /**
     * Proceso principal de emisión de documento fiscal desde una venta
     */
    public function emitirDesdeVenta(Venta $venta): ?DocumentoFiscal
    {
        try {
            return DB::transaction(function () use ($venta) {
                // 1. Determinar tipo de documento (FE o DE)
                $tipo = $this->decidirTipoDocumento($venta);

                // 2. Obtener numeración
                $configNum = ConfiguracionNumeracion::where('empresa_id', $venta->empresa_id)
                    ->where('tipo', $tipo)
                    ->where('activo', true)
                    ->first();

                if (!$configNum) {
                    throw new Exception("No hay configuración de numeración activa para {$tipo}");
                }

                $consecutivo = $configNum->obtenerSiguienteNumero();

                // 3. Crear cabecera del documento fiscal (Local) o recuperar si el controllador ya lo creó provisoriamente
                $docFiscal = $venta->documentoFiscal;
                if (!$docFiscal) {
                    $docFiscal = DocumentoFiscal::create([
                        'empresa_id' => $venta->empresa_id,
                        'venta_id' => $venta->id,
                        'tipo_documento' => $tipo,
                        'prefijo' => $consecutivo['prefijo'],
                        'numero' => $consecutivo['numero'],
                        'numero_completo' => $consecutivo['numero_completo'],
                        'estado' => 'borrador',
                        'cliente_tipo_documento' => $venta->cliente?->persona?->documento?->codigo ?? '13',
                        'cliente_documento' => $venta->cliente?->persona?->numero_documento ?? '222222222222',
                        'cliente_nombre' => $venta->cliente?->persona?->razon_social ?? 'CONSUMIDOR FINAL',
                        'cliente_email' => $venta->cliente?->persona?->email,
                        'subtotal' => $venta->total - $venta->inc_total,
                        'impuesto_inc' => $venta->inc_total,
                        'total' => $venta->total,
                    ]);
                } else {
                    // Update the provisional one with the real consecutive
                    $docFiscal->update([
                        'tipo_documento' => $tipo,
                        'prefijo' => $consecutivo['prefijo'],
                        'numero' => $consecutivo['numero'],
                        'numero_completo' => $consecutivo['numero_completo'],
                        'estado' => 'borrador' // We set it to draft to start the real emission process
                    ]);
                }

                // 4. Crear líneas
                $this->crearLineas($docFiscal, $venta);

                // 5. Enviar al proveedor DIAN
                try {
                    $respuesta = $this->provider->enviarDocumento($docFiscal);

                    if ($respuesta['success']) {
                        $docFiscal->marcarComoEmitido(
                            $respuesta['cufe'] ?? $respuesta['cude'],
                            $respuesta['xml_path'] ?? null,
                            $respuesta['pdf_path'] ?? null
                        );
                        $docFiscal->marcarComoAceptado();

                        Log::info("Documento Fiscal {$docFiscal->numero_completo} emitido correctamente.");
                    } else {
                        throw new Exception($respuesta['error'] ?? 'Error desconocido del proveedor');
                    }
                } catch (Exception $e) {
                    // Fallo de comunicación o rechazo -> Activar Contingencia
                    $this->contingencia->activarContingencia($docFiscal, $e->getMessage());
                }

                return $docFiscal;
            });
        } catch (Exception $e) {
            Log::error("Error crítico en emisión fiscal: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Decidir si es DE o FE según DIAN
     * 
     * Reglas:
     * 1. Venta > 5 UVT ($235,325 en 2025) → FE obligatoria
     * 2. Cliente solicita factura (con NIT o CC) → FE
     * 3. Venta a crédito → FE
     * 4. Resto → DE
     */
    public function decidirTipoDocumento(Venta $venta): string
    {
        // Constante: 5 UVT en pesos (actualizar anualmente)
        // 2025: UVT = $47,065 → 5 UVT = $235,325
        $UMBRAL_5_UVT = config('fiscal.limite_pos_uvt', 5) * config('fiscal.uvt_valor', 47065);

        // REGLA 1: Venta mayor a 5 UVT → FE obligatoria
        if ($venta->total > $UMBRAL_5_UVT) {
            Log::info('FE por umbral 5 UVT', [
                'venta_id' => $venta->id,
                'total' => $venta->total,
            ]);
            return 'FE';
        }

        // REGLA 2: Cliente solicitó factura explícitamente
        if ($venta->solicita_factura) {

            // Validar que tenga documento válido (NIT o CC)
            if (empty($venta->cliente_documento)) {
                Log::warning('Cliente solicitó factura pero no tiene documento', [
                    'venta_id' => $venta->id,
                ]);
                return 'DE'; // Fallback a DE
            }

            // Validar Documento usando Helpers
            $valido = \App\Helpers\DocumentoHelper::validarDocumento(
                $venta->cliente_tipo_doc,
                $venta->cliente_documento
            );

            if (!$valido) {
                Log::warning('Documento inválido, emitiendo DE', [
                    'venta_id' => $venta->id,
                    'tipo_doc' => $venta->cliente_tipo_doc,
                    'documento' => $venta->cliente_documento,
                ]);
                return 'DE';
            }

            Log::info('FE solicitada por cliente', [
                'venta_id' => $venta->id,
                'tipo_doc' => $venta->cliente_tipo_doc,
                'documento' => $venta->cliente_documento,
            ]);
            return 'FE';
        }

        // REGLA 3: Venta a crédito → FE (si aplica)
        // TODO: Implementar cuando exista módulo de crédito

        // REGLA 4: Por defecto → DE (consumidor final)
        Log::info('DE para consumidor final', [
            'venta_id' => $venta->id,
            'total' => $venta->total,
        ]);
        return 'DE';
    }

    /**
     * Mapea los ítems de la venta a líneas fiscales
     */
    private function crearLineas(DocumentoFiscal $doc, Venta $venta): void
    {
        $lineaNum = 1;

        // 1. Boletos de Cine
        if ($venta->asientosCinema->count() > 0) {
            foreach ($venta->asientosCinema as $asiento) {
                DocumentoFiscalLinea::create([
                    'documento_fiscal_id' => $doc->id,
                    'linea' => $lineaNum++,
                    'tipo_item' => 'BOLETO',
                    'codigo' => 'CINE-' . $asiento->funcion->pelicula_id,
                    'descripcion' => "Boleta Cine: " . $asiento->funcion->pelicula->titulo . " - Silla: " . $asiento->codigo_asiento,
                    'cantidad' => 1,
                    'precio_unitario' => $asiento->precio,
                    'subtotal_linea' => $asiento->precio,
                    'aplica_inc' => false,
                    'valor_inc' => 0,
                    'total_linea' => $asiento->precio,
                ]);
            }
        }

        // 2. Productos de Confitería
        foreach ($venta->productos as $producto) {
            $subtotalProducto = $producto->pivot->precio_venta / 1.08;
            $incProducto = $producto->pivot->precio_venta - $subtotalProducto;

            DocumentoFiscalLinea::create([
                'documento_fiscal_id' => $doc->id,
                'linea' => $lineaNum++,
                'tipo_item' => 'PRODUCTO',
                'codigo' => $producto->codigo_barras ?? 'PROD-' . $producto->id,
                'descripcion' => $producto->nombre,
                'cantidad' => $producto->pivot->cantidad,
                'precio_unitario' => $subtotalProducto,
                'subtotal_linea' => $subtotalProducto * $producto->pivot->cantidad,
                'aplica_inc' => true,
                'valor_inc' => $incProducto * $producto->pivot->cantidad,
                'total_linea' => $producto->pivot->precio_venta * $producto->pivot->cantidad,
            ]);
        }
    }
}
