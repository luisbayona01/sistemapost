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
        Schema::table('insumos', function (Blueprint $table) {
            $table->decimal('stock_seguridad', 15, 3)->default(0)->after('stock_minimo');
            $table->decimal('rendimiento', 5, 2)->default(100.00)->comment('Porcentaje de aprovechamiento neto')->after('stock_seguridad');
        });
    }

    public function down(): void
    {
        Schema::table('insumos', function (Blueprint $table) {
            $table->dropColumn(['stock_seguridad', 'rendimiento']);
        });
    }
};
