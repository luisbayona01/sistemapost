<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
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
            if (Schema::hasColumn('cajas', 'efectivo_declarado')) {
                $table->dropColumn('efectivo_declarado');
            }
            if (Schema::hasColumn('cajas', 'tarjeta_declarado')) {
                $table->dropColumn('tarjeta_declarado');
            }
            if (Schema::hasColumn('cajas', 'otros_declarado')) {
                $table->dropColumn('otros_declarado');
            }
        });
    }
};
