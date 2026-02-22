<?php

namespace App\Models;

use App\Enums\TipoTransaccionEnum;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Kardex extends Model
{
    protected $guarded = ['id'];

    protected $table = 'kardex';

    protected $casts = [
        'tipo_transaccion' => TipoTransaccionEnum::class,
        'entrada' => 'integer',
        'salida' => 'integer',
        'saldo' => 'integer',
        'costo_unitario' => 'decimal:2',
    ];

    private const MARGEN_GANANCIA = 0.2;

    /**
     * Relación: Kardex pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Kardex pertenece a un producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación: Kardex pertenece a un insumo
     */
    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }

    /**
     * Global scope: Filtrar kardex por empresa del usuario autenticado
     */
    protected static function booted(): void
    {
        static::addGlobalScope('empresa', function (Builder $query) {
            if (auth()->check() && auth()->user()->empresa_id) {
                $query->where('kardex.empresa_id', auth()->user()->empresa_id);
            }
        });
    }

    /**
     * Scope: Obtener kardex por empresa
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->withoutGlobalScope('empresa')->where('empresa_id', $empresaId);
    }

    /**
     * Scope: Obtener kardex por tipo de transacción
     */
    public function scopeByTipo($query, TipoTransaccionEnum $tipo)
    {
        return $query->where('tipo_transaccion', $tipo);
    }

    /**
     * Scope: Obtener kardex por producto
     */
    public function scopeByProducto($query, int $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    /**
     * Scope: Obtener kardex por insumo
     */
    public function scopeByInsumo($query, int $insumoId)
    {
        return $query->where('insumo_id', $insumoId);
    }

    /**
     * Scope: Obtener kardex en un período
     */
    public function scopeEnPeriodo($query, $inicio, $fin)
    {
        return $query->whereBetween('created_at', [$inicio, $fin]);
    }

    /**
     * Obtener la fecha formateada
     */
    public function getFechaAttribute(): string
    {
        return $this->created_at->format('d/m/Y');
    }

    /**
     * Obtener la hora formateada
     */
    public function getHoraAttribute(): string
    {
        return $this->created_at->format('h:i A');
    }

    /**
     * Obtener el costo total
     */
    public function getCostoTotalAttribute(): float
    {
        return $this->saldo * $this->costo_unitario;
    }

    /**
     * Crear un registro en el Kardex
     */
    public function crearRegistro(array $data, TipoTransaccionEnum $tipo): void
    {
        $entrada = null;
        $salida = null;
        $saldo = null;
        $productoId = $data['producto_id'] ?? null;
        $insumoId = $data['insumo_id'] ?? null;

        $ultimoRegistro = $this->when($productoId, fn($q) => $q->where('producto_id', $productoId))
            ->when($insumoId, fn($q) => $q->where('insumo_id', $insumoId))
            ->latest('id')
            ->first();

        $saldoAnterior = $ultimoRegistro ? $ultimoRegistro->saldo : 0;
        $saldo = $saldoAnterior;

        if ($tipo == TipoTransaccionEnum::Compra || $tipo == TipoTransaccionEnum::Apertura) {
            $entrada = $data['cantidad'];
            $saldo += $entrada;
        } elseif ($tipo == TipoTransaccionEnum::Venta || $tipo == TipoTransaccionEnum::Ajuste || $tipo == TipoTransaccionEnum::Auditoria) {
            // En Auditoría, la 'cantidad' pasada podría ser la diferencia o un ajuste neto. 
            // Para simplicidad, asumimos que 'cantidad' es el movimiento relativo.
            $salida = $data['cantidad'];
            $saldo -= $salida;
        }

        try {
            $this->create([
                'empresa_id' => auth()->check() ? auth()->user()->empresa_id : ($data['empresa_id'] ?? null),
                'producto_id' => $productoId,
                'insumo_id' => $insumoId,
                'tipo_transaccion' => $tipo,
                'descripcion_transaccion' => $data['descripcion'] ?? $this->getDescripcionTransaccion($data, $tipo),
                'entrada' => $entrada,
                'salida' => $salida,
                'saldo' => $saldo,
                'costo_unitario' => $data['costo_unitario'],
                'created_at' => $data['fecha'] ?? now(),
            ]);
        } catch (Exception $e) {
            Log::error('Error al crear un registro en el kardex', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    /**
     * Obtener la descripción según el tipo de Transacción
     */
    private function getDescripcionTransaccion(array $data, TipoTransaccionEnum $tipo): string
    {
        $descripcion = '';
        switch ($tipo) {
            case TipoTransaccionEnum::Apertura:
                $descripcion = 'Apertura del producto';
                break;
            case TipoTransaccionEnum::Compra:
                $descripcion = 'Entrada de producto por la compra n°' . ($data['compra_id'] ?? '');
                break;
            case TipoTransaccionEnum::Venta:
                $descripcion = 'Salida de producto por la venta n°' . ($data['venta_id'] ?? '');
                break;
            case TipoTransaccionEnum::Ajuste:
                $descripcion = 'Ajuste de inventario: ' . ($data['motivo'] ?? '');
                break;
            case TipoTransaccionEnum::Auditoria:
                $descripcion = 'Ajuste por Auditoría n°' . ($data['auditoria_id'] ?? '');
                break;
        }

        return $descripcion;
    }

    /**
     * Obtener el precio de Venta según el costo del Producto
     */
    public function calcularPrecioVenta(int $producto_id): float
    {
        $ultimoRegistro = $this->where('producto_id', $producto_id)
            ->latest('id')
            ->first();

        // Si no hay registros previos, devolvemos el precio actual del producto o 0
        if (!$ultimoRegistro) {
            return (float) Producto::where('id', $producto_id)->value('precio') ?? 0.0;
        }

        $costoUltimoRegistro = (float) $ultimoRegistro->costo_unitario;

        return $costoUltimoRegistro + round($costoUltimoRegistro * self::MARGEN_GANANCIA, 2);
    }
}
