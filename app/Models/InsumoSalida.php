<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoSalida extends Model
{
    use HasFactory;

    protected $fillable = [
        'insumo_id',
        'user_id',
        'cantidad',
        'tipo',
        'motivo',
        'costo_estimado'
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
