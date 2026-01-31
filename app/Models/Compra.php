<?php

namespace App\Models;

use App\Enums\MetodoPagoEnum;
use App\Observers\CompraObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\UploadedFile;

#[ObservedBy(CompraObserver::class)]
class Compra extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'metodo_pago' => MetodoPagoEnum::class,
        'fecha_hora' => 'datetime',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relación: Compra pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Compra pertenece a un proveedor
     */
    public function proveedore(): BelongsTo
    {
        return $this->belongsTo(Proveedore::class);
    }

    /**
     * Relación: Compra pertenece a un comprobante
     */
    public function comprobante(): BelongsTo
    {
        return $this->belongsTo(Comprobante::class);
    }

    /**
     * Relación: Compra contiene múltiples productos
     */
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class)
            ->withTimestamps()
            ->withPivot('cantidad', 'precio_compra', 'fecha_vencimiento');
    }

    /**
     * Relación: Compra fue registrada por un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Global scope: Filtrar compras por empresa del usuario autenticado
     */
    protected static function booted(): void
    {
        static::addGlobalScope('empresa', function (Builder $query) {
            if (auth()->check() && auth()->user()->empresa_id) {
                $query->where('compras.empresa_id', auth()->user()->empresa_id);
            }
        });
    }

    /**
     * Scope: Obtener compras por empresa
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->withoutGlobalScope('empresa')->where('empresa_id', $empresaId);
    }

    /**
     * Scope: Obtener compras en un período
     */
    public function scopeEnPeriodo($query, Carbon $inicio, Carbon $fin)
    {
        return $query->whereBetween('fecha_hora', [$inicio, $fin]);
    }

    /**
     * Scope: Obtener compras por proveedor
     */
    public function scopeByProveedor($query, int $proveedorId)
    {
        return $query->where('proveedore_id', $proveedorId);
    }

    /**
     * Scope: Obtener compras por usuario
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Obtener solo la fecha
     */
    public function getFechaAttribute(): string
    {
        return Carbon::parse($this->fecha_hora)->format('d-m-Y');
    }

    /**
     * Obtener solo la hora
     */
    public function getHoraAttribute(): string
    {
        return Carbon::parse($this->fecha_hora)->format('H:i');
    }

    /**
     * Guardar el archivo en el servidor.
     */
    public function handleUploadFile(UploadedFile $file): string
    {
        // Crear un nombre único
        $name = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'storage/' . $file->storeAs('compras', $name);
        return $path;
    }
}
