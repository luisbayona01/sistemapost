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
        Schema::table('cajas', function (Blueprint $table) {
            $table->enum('estado_cierre', ['normal', 'reabierto_admin'])->default('normal')->after('estado');
            $table->foreignId('reabierto_por_user_id')->nullable()->constrained('users')->onDelete('set null')->after('estado_cierre');
            $table->timestamp('reabierto_at')->nullable()->after('reabierto_por_user_id');
            $table->text('motivo_reapertura')->nullable()->after('reabierto_at');
            $table->integer('cierre_version')->default(1)->after('motivo_reapertura');
            $table->foreignId('cierre_original_id')->nullable()->constrained('cajas')->onDelete('set null')->after('cierre_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropForeign(['reabierto_por_user_id']);
            $table->dropForeign(['cierre_original_id']);
            $table->dropColumn([
                'estado_cierre',
                'reabierto_por_user_id',
                'reabierto_at',
                'motivo_reapertura',
                'cierre_version',
                'cierre_original_id'
            ]);
        });
    }
};
