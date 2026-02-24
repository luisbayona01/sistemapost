<?php

namespace App\Traits;

use App\Models\AuditForge;
use Illuminate\Support\Facades\Request;

trait HasAuditForge
{
    /**
     * Registra un evento en la bóveda de auditoría forense.
     */
    public function logAudit(string $event, $old = null, $new = null)
    {
        return AuditForge::create([
            'empresa_id' => app()->bound('currentTenant') ? app('currentTenant')->id : (auth()->check() ? auth()->user()->empresa_id : null),
            'user_id' => auth()->id(),
            'event' => $event,
            'model_type' => get_class($this),
            'model_id' => $this->id ?? null,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'occurred_at' => now(),
        ]);
    }
}
