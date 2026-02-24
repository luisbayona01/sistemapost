<?php

namespace App\Models;

use App\Scopes\HasEmpresaScope;
use Illuminate\Database\Eloquent\Model;

class AuditForge extends Model
{
    protected $table = 'audit_forge';
    protected $guarded = [];
    public $timestamps = true;

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'occurred_at' => 'datetime'
    ];

    protected static function booted()
    {
        // Aplicar el scope global de multitenancy
        static::addGlobalScope(new HasEmpresaScope());

        // Generar hash inmutable al crear
        static::creating(function ($audit) {
            $data = [
                'empresa_id' => $audit->empresa_id,
                'user_id' => $audit->user_id,
                'event' => $audit->event,
                'model_type' => $audit->model_type,
                'model_id' => $audit->model_id,
                'old_values' => $audit->old_values,
                'new_values' => $audit->new_values,
                'ip_address' => $audit->ip_address,
                'occurred_at' => $audit->occurred_at instanceof \DateTimeInterface
                    ? $audit->occurred_at->format('Y-m-d H:i:s')
                    : ($audit->occurred_at ?? now()->format('Y-m-d H:i:s')),
            ];

            // Usamos la app key como sal para el hash
            $audit->hash = hash('sha256', json_encode($data) . config('app.key'));
        });
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
