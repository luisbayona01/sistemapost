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
        Schema::create('periodos_operativos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresa');
            $table->date('fecha_operativa');
            $table->enum('estado', ['ABIERTO', 'CERRADO'])->default('ABIERTO');
            $table->timestamp('fecha_cierre')->nullable();
            $table->foreignId('cerrado_por')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['empresa_id', 'fecha_operativa']);
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->date('fecha_operativa')->nullable()->after('fecha_hora');
            $table->index('fecha_operativa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('fecha_operativa');
        });
        Schema::dropIfExists('periodos_operativos');
    }
};
