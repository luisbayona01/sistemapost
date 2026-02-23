<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrecioEntrada extends Model
{
    protected $table = 'precios_entradas';

    protected $fillable = ['empresa_id', 'nombre', 'precio', 'activo'];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
