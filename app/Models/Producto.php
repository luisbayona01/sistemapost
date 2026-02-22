<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Traits\HasEmpresaScope;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\ProductoObserver;

#[ObservedBy(ProductoObserver::class)]
class Producto extends Model
{
    use HasFactory, HasEmpresaScope;

    protected $guarded = ['id', 'stock_actual'];

    protected $casts = [
        'precio' => 'decimal:2',
        'gasto_operativo_fijo' => 'decimal:2',
        'porcentaje_impuesto' => 'decimal:2',
        'costo_insumos_total' => 'decimal:2',
        'costo_merma' => 'decimal:2',
        'costo_indirectos' => 'decimal:2',
        'costo_total_unitario' => 'decimal:2',
        'precio_sugerido' => 'decimal:2',
        'margen_ganancia_absoluta' => 'decimal:2',
        'margen_ganancia_porcentual' => 'decimal:2',
        'roi' => 'decimal:2',
        'margen_objetivo' => 'decimal:2',
        'costos_calculados_at' => 'datetime',
        'es_venta_retail' => 'boolean',
        'stock_actual' => 'decimal:3',
    ];

    public function scopeRetail(Builder $query): Builder
    {
        return $query->where('es_venta_retail', true);
    }

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

    protected static function booted(): void
    {
        static::creating(function ($producto) {
            // Si no se proporciona un código, generar uno único
            if (empty($producto->codigo)) {
                $producto->codigo = self::generateUniqueCode();
            }
        });
    }


    /**
     * Genera un código único de forma secuencial (ej: PRO-0001)
     */
    private static function generateUniqueCode(): string
    {
        $ultimoProducto = self::where('codigo', 'like', 'PRO-%')
            ->orderBy('id', 'desc')
            ->first();

        $secuencia = 1;
        if ($ultimoProducto && preg_match('/^PRO-(\d+)$/', $ultimoProducto->codigo, $matches)) {
            $secuencia = (int) $matches[1] + 1;
        }

        do {
            $code = 'PRO-' . str_pad($secuencia, 4, '0', STR_PAD_LEFT);
            $secuencia++;
        } while (self::where('codigo', $code)->exists());

        return $code;
    }

    /**
     * Accesor para obtener el código, nombre y presentación del producto
     */
    public function getNombreCompletoAttribute(): string
    {
        $presentacion = $this->presentacione ? $this->presentacione->sigla : 'N/A';
        return "Código: {$this->codigo} - {$this->nombre} - Presentación: {$presentacion}";
    }

    public function getPrecioFormateadoAttribute(): string
    {
        return number_format((float) $this->precio, 2, '.', ',');
    }

    /**
     * Relación: Producto (como receta) tiene múltiples insumos
     */
    public function insumos(): BelongsToMany
    {
        return $this->belongsToMany(Insumo::class, 'recetas')
            ->withPivot('id', 'cantidad', 'unidad_medida', 'merma_esperada')
            ->withTimestamps();
    }

    /**
     * Calcula y actualiza automáticamente los márgenes de rentabilidad
     */
    public function calcularRentabilidad(): void
    {
        // 1. Calcular costo de insumos (suma de receta)
        $costoInsumos = 0;
        foreach ($this->insumos as $insumo) {
            $cantidad = $insumo->pivot->cantidad ?? 0;
            $costoUnitario = $insumo->costo_unitario ?? 0;
            // factor merma_esperada de la receta (si existe en pivot)
            $mermaReceta = $insumo->pivot->merma_esperada ?? 0;

            // Aplicar rendimiento del insumo si existe (rendimiento = 100 - merma_base_insumo)
            $rendimiento = $insumo->rendimiento ?? 100;
            $factorRendimiento = $rendimiento / 100;

            // Costo ponderado: (cantidad * costo) / rendimiento_neto
            // El rendimiento neto considera el desperdicio base del insumo
            $costoInsumoBase = ($cantidad * $costoUnitario) / $factorRendimiento;

            // Sumar merma específica de la receta si aplica
            $costoInsumoConMermaReceta = $costoInsumoBase * (1 + ($mermaReceta / 100));

            $costoInsumos += $costoInsumoConMermaReceta;
        }

        // 2. Calcular costo de merma general de la empresa (porcentaje sobre insumos)
        $empresa = $this->empresa;
        $porcentajeMerma = $empresa->merma_esperada_porcentaje ?? 0;
        $costoMerma = $costoInsumos * ($porcentajeMerma / 100);

        // 3. Calcular costos indirectos (porcentaje sobre insumos + gasto operativo fijo)
        $porcentajeIndirectos = $empresa->gastos_indirectos_porcentaje ?? 0;
        $costoIndirectos = ($costoInsumos * ($porcentajeIndirectos / 100)) + ($this->gasto_operativo_fijo ?? 0);

        // 4. Costo total unitario
        $costoTotal = $costoInsumos + $costoMerma + $costoIndirectos;

        // 5. Calcular precio sugerido (si hay margen objetivo)
        $margenObjetivo = $this->margen_objetivo ?? 40;
        $precioSugerido = $costoTotal > 0 ? $costoTotal / (1 - ($margenObjetivo / 100)) : 0;

        // 6. Calcular márgenes basados en precio actual
        $precioVenta = $this->precio ?? 0;
        $margenAbsoluto = $precioVenta - $costoTotal;
        $margenPorcentual = $precioVenta > 0 ? ($margenAbsoluto / $precioVenta) * 100 : 0;
        $roi = $costoTotal > 0 ? ($margenAbsoluto / $costoTotal) * 100 : 0;

        // 7. Actualizar campos sin disparar eventos (evita loop infinito)
        $this->updateQuietly([
            'costo_insumos_total' => round($costoInsumos, 2),
            'costo_merma' => round($costoMerma, 2),
            'costo_indirectos' => round($costoIndirectos, 2),
            'costo_total_unitario' => round($costoTotal, 2),
            'precio_sugerido' => round($precioSugerido, 2),
            'margen_ganancia_absoluta' => round($margenAbsoluto, 2),
            'margen_ganancia_porcentual' => round($margenPorcentual, 2),
            'roi' => round($roi, 2),
            'costos_calculados_at' => now(),
        ]);
    }
}
