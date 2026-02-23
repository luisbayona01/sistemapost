<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditoria_id',
        'insumo_id',
        'stock_teorico',
        'stock_fisico',
        'diferencia',
        'valor_diferencia'
    ];

    public function auditoria()
    {
        return $this->belongsTo(AuditoriaInventario::class, 'auditoria_id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }
}
