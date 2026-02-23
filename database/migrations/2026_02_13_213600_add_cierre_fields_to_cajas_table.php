<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            if (!Schema::hasColumn('cajas', 'monto_final_declarado')) {
                $table->decimal('monto_final_declarado', 10, 2)->nullable()->after('monto_inicial');
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
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn(['monto_final_declarado', 'monto_final_esperado', 'diferencia', 'cerrado_por', 'notas_cierre']);
        });
    }
};
