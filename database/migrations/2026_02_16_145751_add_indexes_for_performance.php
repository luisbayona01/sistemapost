<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Asegurar que la columna existe antes de indexarla
        if (!Schema::hasColumn('productos', 'disponible_venta')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->boolean('disponible_venta')->default(true)->after('estado');
            });
        }

        Schema::table('funciones', function (Blueprint $table) {
            $table->index(['empresa_id', 'fecha_hora'], 'idx_funciones_perf_empresa_fecha');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->index(['empresa_id', 'es_venta_retail', 'disponible_venta'], 'idx_productos_perf_retail_disp');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->index(['empresa_id', 'caja_id', 'estado_pago'], 'idx_ventas_perf_caja_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funciones', function (Blueprint $table) {
            $table->dropIndex('idx_funciones_perf_empresa_fecha');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex('idx_productos_perf_retail_disp');
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex('idx_ventas_perf_caja_estado');
        });
    }
};
