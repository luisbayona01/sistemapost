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
        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('subtotal_cine', 12, 2)->default(0)->after('subtotal');
            $table->decimal('subtotal_confiteria', 12, 2)->default(0)->after('subtotal_cine');
            $table->decimal('inc_total', 12, 2)->default(0)->after('impuesto');
            $table->decimal('total_final', 12, 2)->default(0)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['subtotal_cine', 'subtotal_confiteria', 'inc_total', 'total_final']);
        });
    }
};
