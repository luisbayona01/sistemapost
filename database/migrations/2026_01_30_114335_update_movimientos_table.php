<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->foreignId('empresa_id')
                ->after('id')
                ->constrained('empresa')
                ->cascadeOnDelete();

            $table->foreignId('venta_id')
                ->nullable()
                ->after('caja_id')
                ->constrained()
                ->cascadeOnDelete();

            // Agregar índice compuesto para reportes
            $table->index(['empresa_id', 'caja_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropIndex(['empresa_id', 'caja_id', 'created_at']);
            $table->dropForeign(['venta_id']);
            $table->dropColumn('venta_id');
            $table->dropForeign(['empresa_id']);
            $table->dropColumn('empresa_id');
        });
    }
};
