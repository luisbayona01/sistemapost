<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastoOperacional extends Model
{
    protected $table = 'gastos_operacionales';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'monto',
        'periodo',
        'fecha_pago',
        'observaciones'
    ];
}
