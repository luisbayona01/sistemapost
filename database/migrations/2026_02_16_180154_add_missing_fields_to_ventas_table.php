<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'monto_recibido')) {
                $table->decimal('monto_recibido', 10, 2)->nullable()->after('total');
            } else {
                $table->decimal('monto_recibido', 10, 2)->nullable()->change();
            }

            if (!Schema::hasColumn('ventas', 'cambio')) {
                // Si existe vuelto_entregado, lo renombramos
                if (Schema::hasColumn('ventas', 'vuelto_entregado')) {
                    $table->renameColumn('vuelto_entregado', 'cambio');
                } else {
                    $table->decimal('cambio', 10, 2)->nullable()->default(0)->after('monto_recibido');
                }
            }

            if (!Schema::hasColumn('ventas', 'monto_tarifa')) {
                $table->decimal('monto_tarifa', 10, 2)->nullable()->default(0)->after('subtotal');
            }

            if (!Schema::hasColumn('ventas', 'caja_id')) {
                $table->foreignId('caja_id')->nullable()->constrained('cajas')->after('empresa_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasColumn('ventas', 'cambio') && !Schema::hasColumn('ventas', 'vuelto_entregado')) {
                $table->renameColumn('cambio', 'vuelto_entregado');
            }
        });
    }
};
