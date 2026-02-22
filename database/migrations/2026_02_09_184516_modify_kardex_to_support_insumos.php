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
        Schema::table('kardex', function (Blueprint $table) {
            $table->foreignId('producto_id')->nullable()->change();
            $table->foreignId('insumo_id')->nullable()->after('producto_id')->constrained('insumos')->cascadeOnDelete();
            // Change enum to string or add the new value
            $table->string('tipo_transaccion')->change(); // Using string for flexibility with Enums
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kardex', function (Blueprint $table) {
            $table->dropForeign(['insumo_id']);
            $table->dropColumn('insumo_id');
            $table->foreignId('producto_id')->nullable(false)->change();
        });
    }
};
