<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Traits\HasEmpresaScope;

class Caracteristica extends Model
{
    use HasFactory, HasEmpresaScope;

    protected $fillable = ['nombre', 'descripcion', 'estado', 'empresa_id'];

    public function categoria(): HasOne
    {
        return $this->hasOne(Categoria::class);
    }

    public function marca(): HasOne
    {
        return $this->hasOne(Marca::class);
    }

    public function presentacione(): HasOne
    {
        return $this->hasOne(Presentacione::class);
    }
}
