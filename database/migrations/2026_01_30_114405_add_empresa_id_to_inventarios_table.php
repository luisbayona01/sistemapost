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
        Schema::table('inventario', function (Blueprint $table) {
            // Solo agregar empresa_id si no existe
            if (!Schema::hasColumn('inventario', 'empresa_id')) {
                $table->foreignId('empresa_id')
                    ->after('id')
                    ->constrained('empresa')
                    ->cascadeOnDelete();

                // Índice para búsquedas
                $table->index(['empresa_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            if (Schema::hasColumn('inventario', 'empresa_id')) {
                $table->dropForeign(['empresa_id']);
                $table->dropIndex(['empresa_id']);
                $table->dropColumn('empresa_id');
            }
        });
    }
};
