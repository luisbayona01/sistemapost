<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoLote extends Model
{
    use HasFactory;

    protected $fillable = [
        'insumo_id',
        'numero_lote',
        'cantidad_inicial',
        'cantidad_actual',
        'costo_unitario',
        'fecha_vencimiento'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'cantidad_actual' => 'decimal:3',
        'costo_unitario' => 'decimal:2'
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }
}
