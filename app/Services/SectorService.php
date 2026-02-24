<?php

namespace App\Services;

use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;

class SectorService
{
    /**
     * Get current sector for the authenticated user's company
     */
    public function getCurrentSector(): string
    {
        if (Auth::check() && Auth::user()->empresa) {
            return Auth::user()->empresa->sector ?? 'cine';
        }
        return 'cine';
    }

    /**
     * Check if current sector is cinema
     */
    public function isCinema(): bool
    {
        return $this->getCurrentSector() === 'cine';
    }

    /**
     * Check if current sector is veterinaria
     */
    public function isVeterinaria(): bool
    {
        return $this->getCurrentSector() === 'veterinaria';
    }

    /**
     * Get label for dynamic sections
     */
    public function getLabel(string $key): string
    {
        $sector = $this->getCurrentSector();

        $labels = [
            'cine' => [
                'retail' => 'Confitería',
                'dulceria' => 'Dulcería',
                'main_activities' => 'Cartelera y Funciones',
                'inventory' => 'Inventario Insumos',
            ],
            'veterinaria' => [
                'retail' => 'Productos',
                'dulceria' => 'Alimentos/Farmacia',
                'main_activities' => 'Consultas y Procedimientos',
                'inventory' => 'Inventario Mascotas',
            ]
        ];

        return $labels[$sector][$key] ?? $labels['cine'][$key] ?? $key;
    }

    /**
     * Determine if a menu item should be visible
     */
    public function shouldShow(string $feature): bool
    {
        $sector = $this->getCurrentSector();

        $visibility = [
            'cine' => [
                'cartelera' => true,
                'funciones' => true,
                'salas' => true,
                'peliculas' => true,
                'ventas_mixtas' => true,
            ],
            'veterinaria' => [
                'cartelera' => false,
                'funciones' => false,
                'salas' => false,
                'peliculas' => false,
                'ventas_mixtas' => false,
                'fichas_clinicas' => true,
            ]
        ];

        return $visibility[$sector][$feature] ?? true;
    }
}
