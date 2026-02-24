<?php

namespace App\Traits;

use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;

trait SectorAware
{
    /**
     * Get the current sector of the user's company
     */
    public function getSector(): string
    {
        return Auth::user()->empresa->sector ?? 'cine';
    }

    /**
     * Check if current sector is cinema
     */
    public function isCinema(): bool
    {
        return $this->getSector() === 'cine';
    }

    /**
     * Check if current sector is veterinaria
     */
    public function isVeterinaria(): bool
    {
        return $this->getSector() === 'veterinaria';
    }

    /**
     * Get dynamic labels based on sector
     */
    public function getSectorLabel(string $key): string
    {
        $sector = $this->getSector();

        $labels = [
            'cine' => [
                'retail' => 'Confitería',
                'dulceria' => 'Dulcería',
                'inventory' => 'Insumos Cine',
                'selling_unit' => 'Unidad',
            ],
            'veterinaria' => [
                'retail' => 'Productos',
                'dulceria' => 'Farmacia/PetShop',
                'inventory' => 'Productos Veterinarios',
                'selling_unit' => 'Unidad/Servicio',
            ]
        ];

        return $labels[$sector][$key] ?? $labels['cine'][$key] ?? $key;
    }
}
