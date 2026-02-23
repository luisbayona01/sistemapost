<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            // Campos de cierre básicos
            if (!Schema::hasColumn('cajas', 'fecha_cierre')) {
                // Si existe fecha_cierre pero no fecha_cierre, podemos renombrar o simplemente agregar.
                // El usuario pidió agregar. Usaremos fecha_apertura como referencia si fecha_apertura no existe.
                $after = Schema::hasColumn('cajas', 'fecha_apertura') ? 'fecha_apertura' : 'fecha_apertura';
                $table->timestamp('fecha_cierre')->nullable()->after($after);
            }

            if (!Schema::hasColumn('cajas', 'monto_final_declarado')) {
                $after = Schema::hasColumn('cajas', 'monto_inicial') ? 'monto_inicial' : 'monto_inicial';
                $table->decimal('monto_final_declarado', 10, 2)->nullable()->after($after);
            }

            if (!Schema::hasColumn('cajas', 'monto_final_esperado')) {
                $table->decimal('monto_final_esperado', 10, 2)->nullable()->after('monto_final_declarado');
            }

            if (!Schema::hasColumn('cajas', 'diferencia')) {
                $table->decimal('diferencia', 10, 2)->nullable()->after('monto_final_esperado');
            }

            if (!Schema::hasColumn('cajas', 'cerrado_por')) {
                $table->foreignId('cerrado_por')->nullable()->constrained('users')->after('user_id');
            }

            if (!Schema::hasColumn('cajas', 'notas_cierre')) {
                $table->text('notas_cierre')->nullable()->after('diferencia');
            }

            // Desglose por método de pago
            if (!Schema::hasColumn('cajas', 'efectivo_declarado')) {
                $table->decimal('efectivo_declarado', 10, 2)->nullable()->after('monto_final_declarado');
            }

            if (!Schema::hasColumn('cajas', 'tarjeta_declarado')) {
                $table->decimal('tarjeta_declarado', 10, 2)->nullable()->after('efectivo_declarado');
            }

            if (!Schema::hasColumn('cajas', 'otros_declarado')) {
                $table->decimal('otros_declarado', 10, 2)->nullable()->after('tarjeta_declarado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $columnas = [
                'fecha_cierre',
                'monto_final_declarado',
                'monto_final_esperado',
                'diferencia',
                'cerrado_por',
                'notas_cierre',
                'efectivo_declarado',
                'tarjeta_declarado',
                'otros_declarado',
            ];

            foreach ($columnas as $columna) {
                if (Schema::hasColumn('cajas', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
};
