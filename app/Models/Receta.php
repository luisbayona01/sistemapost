<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Receta extends Pivot
{
    protected $table = 'recetas';

    protected $fillable = [
        'producto_id',
        'insumo_id',
        'cantidad',
        'unidad_medida',
        'merma_esperada'
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }
}
