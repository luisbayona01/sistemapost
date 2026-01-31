<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Producto extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    /**
     * Relación: Producto pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Producto pertenece a múltiples compras
     */
    public function compras(): BelongsToMany
    {
        return $this->belongsToMany(Compra::class)
            ->withTimestamps()
            ->withPivot('cantidad', 'precio_compra', 'fecha_vencimiento');
    }

    /**
     * Relación: Producto pertenece a múltiples ventas
     */
    public function ventas(): BelongsToMany
    {
        return $this->belongsToMany(Venta::class)
            ->withTimestamps()
            ->withPivot('cantidad', 'precio_venta', 'tarifa_unitaria');
    }

    /**
     * Relación: Producto pertenece a una categoría
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación: Producto pertenece a una marca
     */
    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    /**
     * Relación: Producto pertenece a una presentación
     */
    public function presentacione(): BelongsTo
    {
        return $this->belongsTo(Presentacione::class);
    }

    /**
     * Relación: Producto tiene un registro de inventario
     */
    public function inventario(): HasOne
    {
        return $this->hasOne(Inventario::class);
    }

    /**
     * Relación: Producto tiene múltiples registros kardex
     */
    public function kardex(): HasMany
    {
        return $this->hasMany(Kardex::class);
    }

    /**
     * Global scope: Filtrar productos por empresa del usuario autenticado
     */
    protected static function booted(): void
    {
        static::addGlobalScope('empresa', function (Builder $query) {
            if (auth()->check() && auth()->user()->empresa_id) {
                $query->where('productos.empresa_id', auth()->user()->empresa_id);
            }
        });

        static::creating(function ($producto) {
            // Si no se proporciona un código, generar uno único
            if (empty($producto->codigo)) {
                $producto->codigo = self::generateUniqueCode();
            }
        });
    }


    /**
     * Genera un código único para el producto
     */
    private static function generateUniqueCode(): string
    {
        do {
            $code = str_pad(random_int(0, 9999999999), 12, '0', STR_PAD_LEFT);
        } while (self::where('codigo', $code)->exists());

        return $code;
    }

    /**
     * Accesor para obtener el código, nombre y presentación del producto
     */
    public function getNombreCompletoAttribute(): string
    {
        return "Código: {$this->codigo} - {$this->nombre} - Presentación: {$this->presentacione->sigla}";
    }

    /**
     * Obtener el precio con formato
     */
    public function getPrecioFormateadoAttribute(): string
    {
        return number_format($this->precio, 2, '.', ',');
    }
}
