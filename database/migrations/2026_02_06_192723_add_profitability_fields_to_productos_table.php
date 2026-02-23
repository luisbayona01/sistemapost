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
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('costo_insumos_total', 10, 2)->default(0)->after('gasto_operativo_fijo');
            $table->decimal('costo_merma', 10, 2)->default(0)->after('costo_insumos_total');
            $table->decimal('costo_indirectos', 10, 2)->default(0)->after('costo_merma');
            $table->decimal('costo_total_unitario', 10, 2)->default(0)->after('costo_indirectos');
            $table->decimal('precio_sugerido', 10, 2)->nullable()->after('costo_total_unitario');
            $table->decimal('margen_ganancia_absoluta', 10, 2)->default(0)->after('precio');
            $table->decimal('margen_ganancia_porcentual', 8, 2)->default(0)->after('margen_ganancia_absoluta');
            $table->decimal('roi', 8, 2)->default(0)->after('margen_ganancia_porcentual');
            $table->timestamp('costos_calculados_at')->nullable()->after('roi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'costo_insumos_total',
                'costo_merma',
                'costo_indirectos',
                'costo_total_unitario',
                'precio_sugerido',
                'margen_ganancia_absoluta',
                'margen_ganancia_porcentual',
                'roi',
                'costos_calculados_at'
            ]);
        });
    }
};
