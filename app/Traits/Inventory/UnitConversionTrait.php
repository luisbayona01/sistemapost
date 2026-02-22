<?php

namespace App\Traits\Inventory;

trait UnitConversionTrait
{
    /**
     * Factores de conversión referenciados a la unidad base (g, ml, und)
     */
    protected $conversionFactors = [
        'mass' => [
            'g' => 1,
            'kg' => 1000,
            'oz' => 28.3495,
            'lb' => 453.592
        ],
        'volume' => [
            'ml' => 1,
            'l' => 1000,
            'oz' => 29.5735 // Fl. Oz
        ],
        'count' => [
            'und' => 1
        ]
    ];

    /**
     * Convierte una cantidad de una unidad a otra
     */
    public function convert($quantity, $fromUnit, $toUnit)
    {
        if ($fromUnit === $toUnit)
            return $quantity;

        $typeFrom = $this->getUnitType($fromUnit);
        $typeTo = $this->getUnitType($toUnit);

        if ($typeFrom !== $typeTo || $typeFrom === null) {
            throw new \Exception("Incompatibilidad de unidades: No se puede convertir {$fromUnit} a {$toUnit}");
        }

        // Convertir a base
        $baseValue = $quantity * $this->conversionFactors[$typeFrom][$fromUnit];
        // Convertir de base a destino
        return $baseValue / $this->conversionFactors[$typeTo][$toUnit];
    }

    private function getUnitType($unit)
    {
        foreach ($this->conversionFactors as $type => $units) {
            if (array_key_exists($unit, $units))
                return $type;
        }
        return null;
    }
}
