<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasEmpresaScope;

class Ubicacione extends Model
{
    use HasEmpresaScope;

    protected $fillable = ['nombre', 'empresa_id'];

    public function inventario(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }
}
