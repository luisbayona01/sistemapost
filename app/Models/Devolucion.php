<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';

    protected $fillable = [
        'empresa_id',
        'venta_id',
        'user_id',
        'tipo',
        'monto_devuelto',
        'motivo',
        'es_excepcional',
        'autorizacion_nota',
    ];

    protected $casts = [
        'monto_devuelto' => 'decimal:2',
        'es_excepcional' => 'boolean',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(DevolucionItem::class);
    }
}
