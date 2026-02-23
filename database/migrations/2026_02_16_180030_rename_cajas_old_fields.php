<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            if (Schema::hasColumn('cajas', 'fecha_apertura') && !Schema::hasColumn('cajas', 'fecha_apertura')) {
                $table->renameColumn('fecha_apertura', 'fecha_apertura');
            }
            if (Schema::hasColumn('cajas', 'monto_inicial') && !Schema::hasColumn('cajas', 'monto_inicial')) {
                $table->renameColumn('monto_inicial', 'monto_inicial');
            }
            // fecha_cierre already handled by previous migration adding fecha_cierre if null? 
            // Actually previous migration added fecha_cierre. Let's make sure we don't have duplicates.
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            if (Schema::hasColumn('cajas', 'fecha_apertura')) {
                $table->renameColumn('fecha_apertura', 'fecha_apertura');
            }
            if (Schema::hasColumn('cajas', 'monto_inicial')) {
                $table->renameColumn('monto_inicial', 'monto_inicial');
            }
        });
    }
};
