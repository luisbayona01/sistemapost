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
            $table->timestamp('inventario_descontado_at')->nullable()->after('estado_pago');
            $table->index('inventario_descontado_at');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex(['inventario_descontado_at']);
            $table->dropColumn('inventario_descontado_at');
        });
    }
};
