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
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresa')->onDelete('cascade');
            $table->string('key')->index();           // ej: 'reserva_web', 'nomina', 'restaurante'
            $table->boolean('enabled')->default(false);
            $table->json('metadata')->nullable();     // para valores extras (ej: porcentaje descuento)
            $table->timestamp('enabled_at')->nullable();
            $table->timestamps();

            $table->unique(['empresa_id', 'key']);    // un flag por tenant
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_flags');
    }
};
