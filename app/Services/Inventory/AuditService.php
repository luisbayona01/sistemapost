<?php

namespace App\Services\Inventory;

use App\Models\Producto;

class AuditService
{
    /**
     * Selecciona 5 productos aleatorios para auditoría ciega
     */
    public function getDailyAuditChallenge($empresaId)
    {
        return Producto::retail()
            ->where('empresa_id', $empresaId)
            ->inRandomOrder()
            ->take(5)
            ->with(['inventario'])
            ->get();
    }
}
