<?php

namespace App\Helpers;

use App\Models\BusinessConfiguration;
use Illuminate\Support\Facades\Auth;

class ModuleHelper
{
    /**
     * Verificar si un módulo está habilitado para la empresa actual
     */
    public static function isEnabled(string $module): bool
    {
        $empresaId = auth()->user()?->empresa_id;

        if (!$empresaId) {
            return false;
        }

        $config = BusinessConfiguration::where('empresa_id', $empresaId)->first();

        return $config ? $config->isModuleEnabled($module) : false;
    }
}
