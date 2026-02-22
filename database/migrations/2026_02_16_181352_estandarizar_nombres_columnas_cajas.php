<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Primero, renombrar columnas si tienen nombres inconsistentes

        // Si existe 'fecha_hora_apertura', cambiar a 'fecha_apertura'
        if (Schema::hasColumn('cajas', 'fecha_hora_apertura') && !Schema::hasColumn('cajas', 'fecha_apertura')) {
            Schema::table('cajas', function (Blueprint $table) {
                $table->renameColumn('fecha_hora_apertura', 'fecha_apertura');
            });
        }

        // Si existe 'fecha_hora_cierre', cambiar a 'fecha_cierre'
        if (Schema::hasColumn('cajas', 'fecha_hora_cierre') && !Schema::hasColumn('cajas', 'fecha_cierre')) {
            Schema::table('cajas', function (Blueprint $table) {
                $table->renameColumn('fecha_hora_cierre', 'fecha_cierre');
            });
        }

        // Ahora agregar campos faltantes
        Schema::table('cajas', function (Blueprint $table) {
            if (!Schema::hasColumn('cajas', 'fecha_apertura')) {
                $table->timestamp('fecha_apertura')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('cajas', 'fecha_cierre')) {
                $table->timestamp('fecha_cierre')->nullable()->after('fecha_apertura');
            }

            if (!Schema::hasColumn('cajas', 'monto_inicial')) {
                $table->decimal('monto_inicial', 10, 2)->default(0)->after('fecha_apertura');
            }

            if (!Schema::hasColumn('cajas', 'monto_final_declarado')) {
                $table->decimal('monto_final_declarado', 10, 2)->nullable()->after('monto_inicial');
            }

            if (!Schema::hasColumn('cajas', 'monto_final_esperado')) {
                $table->decimal('monto_final_esperado', 10, 2)->nullable()->after('monto_final_declarado');
            }

            if (!Schema::hasColumn('cajas', 'diferencia')) {
                $table->decimal('diferencia', 10, 2)->nullable()->after('monto_final_esperado');
            }

            if (!Schema::hasColumn('cajas', 'estado')) {
                $table->enum('estado', ['ABIERTA', 'CERRADA'])->default('ABIERTA')->after('diferencia');
            }

            if (!Schema::hasColumn('cajas', 'cerrado_por')) {
                $table->foreignId('cerrado_por')->nullable()->constrained('users')->after('user_id');
            }

            if (!Schema::hasColumn('cajas', 'notas_cierre')) {
                $table->text('notas_cierre')->nullable()->after('diferencia');
            }

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
        // No hacer nada en el rollback para no perder datos
    }
};
