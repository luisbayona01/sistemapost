<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * FASE 4.5 - Arqueo completo con validación de datáfono
     */
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            // Campos para arqueo de tarjeta/datáfono
            if (!Schema::hasColumn('cajas', 'tarjeta_declarada')) {
                $table->decimal('tarjeta_declarada', 10, 2)->nullable()->after('monto_final_declarado')
                    ->comment('Total de vouchers/datáfono declarado por el cajero');
            }

            if (!Schema::hasColumn('cajas', 'tarjeta_esperada')) {
                $table->decimal('tarjeta_esperada', 10, 2)->nullable()->after('tarjeta_declarada')
                    ->comment('Total de ventas con tarjeta según el sistema');
            }

            if (!Schema::hasColumn('cajas', 'diferencia_tarjeta')) {
                $table->decimal('diferencia_tarjeta', 10, 2)->nullable()->after('diferencia')
                    ->comment('Diferencia entre tarjeta declarada y esperada');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn(['tarjeta_declarada', 'tarjeta_esperada', 'diferencia_tarjeta']);
        });
    }
};
