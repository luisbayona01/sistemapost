<?php

namespace App\Observers;

use App\Models\Producto;

class ProductoObserver
{
    /**
     * Handle the Producto "saved" event.
     */
    public function saved(Producto $producto): void
    {
        // Solo recalcular si el producto tiene receta (insumos asociados)
        if ($producto->insumos()->exists()) {
            $producto->calcularRentabilidad();
        }
    }

    /**
     * Handle the Producto "created" event.
     */
    public function created(Producto $producto): void
    {
        //
    }

    /**
     * Handle the Producto "updated" event.
     */
    public function updated(Producto $producto): void
    {
        //
    }

    /**
     * Handle the Producto "deleted" event.
     */
    public function deleted(Producto $producto): void
    {
        //
    }

    /**
     * Handle the Producto "restored" event.
     */
    public function restored(Producto $producto): void
    {
        //
    }

    /**
     * Handle the Producto "force deleted" event.
     */
    public function forceDeleted(Producto $producto): void
    {
        //
    }
}
