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
        Schema::table('funcion_asientos', function (Blueprint $table) {
            if (!Schema::hasColumn('funcion_asientos', 'empresa_id')) {
                $table->unsignedBigInteger('empresa_id')->nullable()->index()->after('id');
            }
        });

        // Poblar empresa_id desde la función
        DB::statement("
            UPDATE funcion_asientos fa 
            INNER JOIN funciones f ON f.id = fa.funcion_id 
            SET fa.empresa_id = f.empresa_id 
            WHERE fa.empresa_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funcion_asientos', function (Blueprint $table) {
            $table->dropColumn('empresa_id');
        });
    }
};
