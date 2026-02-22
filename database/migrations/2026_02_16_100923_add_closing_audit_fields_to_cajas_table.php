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
            // Check if columns exist before adding them to avoid errors if re-running or in partial state
            if (!Schema::hasColumn('cajas', 'cierre_user_id')) {
                $table->foreignId('cierre_user_id')->nullable()->constrained('users')->onDelete('set null')->after('cerrado_por');
            }
            if (!Schema::hasColumn('cajas', 'cierre_at')) {
                $table->timestamp('cierre_at')->nullable()->after('fecha_cierre');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropForeign(['cierre_user_id']);
            $table->dropColumn(['cierre_user_id', 'cierre_at']);
        });
    }
};
