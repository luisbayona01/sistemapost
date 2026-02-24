<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditForge;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    /**
     * Lista los registros de la Bóveda de Auditoría Forense.
     */
    public function index()
    {
        // El Super admin debería poder ver todos, pero el scope global lo limita al tenant.
        // Como Super Admin (Root), usaremos withoutGlobalScopes() si es necesario.
        $audits = AuditForge::withoutGlobalScopes()
            ->latest()
            ->paginate(50);

        return view('superadmin.audits.index', compact('audits'));
    }

    /**
     * Verifica la integridad de un registro.
     */
    public function verify($id)
    {
        $audit = AuditForge::withoutGlobalScopes()->findOrFail($id);

        $data = [
            'empresa_id' => $audit->empresa_id,
            'user_id' => $audit->user_id,
            'event' => $audit->event,
            'model_type' => $audit->model_type,
            'model_id' => $audit->model_id,
            'old_values' => $audit->old_values,
            'new_values' => $audit->new_values,
            'ip_address' => $audit->ip_address,
            'occurred_at' => $audit->occurred_at->format('Y-m-d H:i:s'),
        ];

        $expectedHash = hash('sha256', json_encode($data) . config('app.key'));
        $isValid = ($expectedHash === $audit->hash);

        return response()->json([
            'id' => $id,
            'is_valid' => $isValid,
            'current_hash' => $audit->hash,
            'expected_hash' => $expectedHash
        ]);
    }
}
