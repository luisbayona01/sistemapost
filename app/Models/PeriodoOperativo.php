<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodoOperativo extends Model
{
    protected $table = 'periodos_operativos';
    protected $guarded = ['id'];

    protected $casts = [
        'fecha_operativa' => 'date',
        'fecha_cierre' => 'datetime',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }
}
