<?php

namespace App\Observers;

use App\Services\MoneyService;
use Illuminate\Database\Eloquent\Model;

class MoneyRoundingObserver
{
    /**
     * Se ejecuta antes de guardar cualquier modelo observado.
     */
    public function saving(Model $model): void
    {
        // Campos comunes a redondear en Ventas y Documentos Fiscales
        $fields = ['subtotal', 'impuesto', 'total', 'total_final', 'inc_total', 'impuesto_inc', 'monto_recibido'];

        foreach ($fields as $field) {
            if (isset($model->{$field})) {
                $model->{$field} = MoneyService::roundToPeso($model->{$field});
            }
        }
    }
}
