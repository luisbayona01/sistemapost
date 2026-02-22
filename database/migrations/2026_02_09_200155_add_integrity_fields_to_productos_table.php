<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->boolean('es_venta_retail')->default(true)->after('estado');
            $table->decimal('stock_actual', 15, 3)->nullable()->after('es_venta_retail');
        });

        // Sync current stock from inventario table to productos table
        $productos = DB::table('productos')->get();
        foreach ($productos as $producto) {
            $inventario = DB::table('inventario')
                ->where('producto_id', $producto->id)
                ->first();

            $esServicio = str_starts_with($producto->codigo, 'TICKET_CINEMA');

            DB::table('productos')
                ->where('id', $producto->id)
                ->update([
                    'stock_actual' => $inventario ? $inventario->cantidad : null,
                    'es_venta_retail' => !$esServicio
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['es_venta_retail', 'stock_actual']);
        });
    }
};
