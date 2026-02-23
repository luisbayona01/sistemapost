<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionItem extends Model
{
    protected $table = 'devolucion_items';

    protected $fillable = [
        'devolucion_id',
        'tipo_item',
        'funcion_asiento_id',
        'producto_id',
        'cantidad',
        'monto',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'monto' => 'decimal:2',
    ];

    public function funcion_asiento()
    {
        return $this->belongsTo(FuncionAsiento::class);
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
