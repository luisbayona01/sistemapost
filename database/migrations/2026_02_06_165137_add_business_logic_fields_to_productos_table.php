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
            $table->decimal('gasto_operativo_fijo', 12, 2)->default(0)->after('precio');
            $table->enum('tipo_impuesto', ['IVA', 'IMPOCONSUMO', 'EXENTO'])->default('EXENTO')->after('gasto_operativo_fijo');
            $table->decimal('porcentaje_impuesto', 5, 2)->default(0)->after('tipo_impuesto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['gasto_operativo_fijo', 'tipo_impuesto', 'porcentaje_impuesto']);
        });
    }
};
