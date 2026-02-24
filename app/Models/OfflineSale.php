<?php

namespace App\Models;

use App\Traits\HasEmpresaScope;
use Illuminate\Database\Eloquent\Model;

class OfflineSale extends Model
{
    use HasEmpresaScope;

    protected $fillable = [
        'uuid',
        'empresa_id',
        'user_id',
        'local_id',
        'fecha_local',
        'data_json',
        'total',
        'estado',
        'error_message',
        'venta_id'
    ];

    protected $casts = [
        'data_json' => 'array',
        'fecha_local' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
