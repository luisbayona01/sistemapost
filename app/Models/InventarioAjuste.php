<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioAjuste extends Model
{
    protected $fillable = [
        'empresa_id',
        'user_id',
        'insumo_id',
        'producto_id',
        'cantidad',
        'tipo',
        'motivo',
        'observaciones'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
