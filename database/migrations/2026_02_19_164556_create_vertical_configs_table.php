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
        Schema::create('vertical_configs', function (Blueprint $table) {
            $table->unsignedBigInteger('empresa_id')->primary(); // o tenant_id, pero se usa empresa_id en el proyecto
            $table->enum('vertical', ['cine', 'restaurante', 'estadio', 'retail', 'evento'])->default('cine');
            $table->json('features');
            $table->json('fiscal_provider')->nullable();

            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vertical_configs');
    }
};
