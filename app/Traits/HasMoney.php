<?php

namespace App\Traits;

use App\Services\MoneyService;

trait HasMoney
{
    /**
     * Accessor para obtener cualquier monto formateado en COP.
     * Uso: $modelo->formatMoney('total')
     */
    public function formatMoney(string $field): string
    {
        return MoneyService::formatCOP($this->{$field} ?? 0);
    }

    /**
     * Mutator sugerido para asegurar que los montos se guarden redondeados.
     */
    protected function setRoundedAmount(string $field, $value): void
    {
        $this->attributes[$field] = MoneyService::roundToPeso($value);
    }
}
