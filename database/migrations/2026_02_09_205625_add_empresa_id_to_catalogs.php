<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['caracteristicas', 'categorias', 'marcas', 'presentaciones', 'ubicaciones'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableGroup) {
                $tableGroup->foreignId('empresa_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            });

            // Asignar a la empresa 1 por defecto para datos existentes
            DB::table($table)->update(['empresa_id' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['caracteristicas', 'categorias', 'marcas', 'presentaciones', 'ubicaciones'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableGroup) {
                $tableGroup->dropForeign(['empresa_id']);
                $tableGroup->dropColumn('empresa_id');
            });
        }
    }
};
