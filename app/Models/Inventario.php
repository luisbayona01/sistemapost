<?php

namespace App\Models;

use App\Observers\InventarioObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\HasEmpresaScope;

#[ObservedBy([InventarioObserver::class])]
class Inventario extends Model
{
    use HasEmpresaScope;

    protected $guarded = ['id'];

    protected $table = 'inventario';

    protected $casts = [
        'fecha_vencimiento' => 'datetime',
        'cantidad' => 'integer',
        'stock_minimo' => 'integer',
    ];

    /**
     * Relación: Inventario pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Inventario pertenece a una ubicación
     */
    public function ubicacione(): BelongsTo
    {
        return $this->belongsTo(Ubicacione::class);
    }

    /**
     * Relación: Inventario pertenece a un producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Scope: Obtener inventarios por empresa
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->withoutGlobalScope('empresa')->where('empresa_id', $empresaId);
    }

    /**
     * Scope: Obtener inventarios con stock bajo
     */
    public function scopeStockBajo($query)
    {
        return $query->where('cantidad', '<=', function ($subquery) {
            $subquery->select('stock_minimo');
        });
    }

    /**
     * Scope: Obtener inventarios por ubicación
     */
    public function scopeByUbicacion($query, int $ubicacionId)
    {
        return $query->where('ubicacione_id', $ubicacionId);
    }

    /**
     * Scope: Obtener inventarios con vencimiento próximo
     */
    public function scopeProximoVencimiento($query, int $diasAnterior = 7)
    {
        $fecha = now()->addDays($diasAnterior);
        return $query->where('fecha_vencimiento', '<=', $fecha)
            ->where('fecha_vencimiento', '>', now());
    }

    /**
     * Obtener la fecha de vencimiento formateada
     */
    public function getFechaVencimientoFormatAttribute(): string
    {
        return $this->fecha_vencimiento ? $this->fecha_vencimiento->format('d/m/Y') : '';
    }

    /**
     * Verificar si el producto está vencido
     */
    public function estaVencido(): bool
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento < now();
    }

    /**
     * Verificar si el stock está bajo
     */
    public function esStockBajo(): bool
    {
        return $this->cantidad <= $this->stock_minimo;
    }

    /**
     * Aumentar el stock
     */
    public function aumentarStock(int $cantidad): self
    {
        $this->cantidad += $cantidad;
        $this->save();

        return $this;
    }

    /**
     * Disminuir el stock
     */
    public function disminuirStock(int $cantidad): self
    {
        $this->cantidad -= $cantidad;
        $this->save();

        return $this;
    }
}
