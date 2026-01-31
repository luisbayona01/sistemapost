<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega tarifa_unitaria para registrar la tarifa aplicada a cada producto
     * en cada venta. Importante para auditoría y reportes históricos.
     */
    public function up(): void
    {
        Schema::table('producto_venta', function (Blueprint $table) {
            $table->decimal('tarifa_unitaria', 10, 2)
                ->default(0)
                ->after('precio_venta')
                ->comment('Tarifa aplicada a este producto en esta venta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('producto_venta', function (Blueprint $table) {
            $table->dropColumn('tarifa_unitaria');
        });
    }
};
