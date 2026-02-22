<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasEmpresaScope;

class Insumo extends Model
{
    use HasFactory, HasEmpresaScope;

    protected $fillable = [
        'empresa_id',
        'nombre',
        'codigo',
        'unidad_medida',
        'costo_unitario',
        'stock_actual',
        'stock_minimo'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Relationships can be added here
    public function lotes()
    {
        return $this->hasMany(InsumoLote::class)->orderBy('created_at', 'asc');
    }

    public function salidas()
    {
        return $this->hasMany(InsumoSalida::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'recetas')
            ->withPivot('cantidad', 'unidad_medida')
            ->withTimestamps();
    }
}
