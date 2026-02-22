<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->enum('business_type', ['cinema', 'restaurant', 'bakery', 'bar', 'retail', 'generic'])->default('generic');
            $table->json('modules_enabled')->comment('Módulos activos: cinema, pos, inventory, reports, api');
            $table->json('settings')->nullable()->comment('Configuraciones específicas del negocio');
            $table->timestamps();

            $table->unique('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_configurations');
    }
};
