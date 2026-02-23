<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasEmpresaScope;

class AuditoriaInventario extends Model
{
    use HasFactory, HasEmpresaScope;

    protected $table = 'auditorias_inventario';

    protected $fillable = [
        'empresa_id',
        'user_id',
        'fecha_auditoria',
        'estado',
        'total_diferencia_valor'
    ];

    protected $casts = [
        'fecha_auditoria' => 'datetime'
    ];

    public function detalles()
    {
        return $this->hasMany(AuditoriaDetalle::class, 'auditoria_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
