<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessConfiguration extends Model
{
    protected $fillable = [
        'empresa_id',
        'business_type',
        'modules_enabled',
        'settings',
    ];

    protected $casts = [
        'modules_enabled' => 'array',
        'settings' => 'array',
    ];

    /**
     * Relación con Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Verificar si un módulo está habilitado
     */
    public function isModuleEnabled(string $module): bool
    {
        $modules = $this->modules_enabled ?? [];
        return !empty($modules[$module]);
    }

    /**
     * Habilitar un módulo
     */
    public function enableModule(string $module): void
    {
        $modules = $this->modules_enabled ?? [];
        $modules[$module] = true;
        $this->modules_enabled = $modules;
        $this->save();
    }

    /**
     * Deshabilitar un módulo
     */
    public function disableModule(string $module): void
    {
        $modules = $this->modules_enabled ?? [];
        $modules[$module] = false;
        $this->modules_enabled = $modules;
        $this->save();
    }
}
