<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaCompra extends Model
{
    protected $table = 'facturas_compra';

    protected $fillable = [
        'empresa_id',
        'user_id',
        'proveedor_id',
        'numero_factura',
        'fecha_compra',
        'total_pagado',
        'impuesto_tipo',
        'impuesto_porcentaje',
        'impuesto_valor',
        'subtotal_calculado',
        'notas'
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'total_pagado' => 'decimal:2',
        'impuesto_porcentaje' => 'decimal:2',
        'impuesto_valor' => 'decimal:2',
        'subtotal_calculado' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedore::class);
    }

    public function movimientos()
    {
        return $this->hasMany(InventarioMovimiento::class, 'factura_id');
    }
}
