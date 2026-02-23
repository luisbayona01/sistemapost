<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->string('nombre', 100);
            $table->decimal('precio', 10, 2);
            $table->json('dias_semana')->nullable()
                ->comment('0=Dom,1=Lun,2=Mar,3=Mie,4=Jue,5=Vie,6=Sab');
            $table->boolean('aplica_festivos')->default(false);
            $table->boolean('es_default')->default(false);
            $table->boolean('activa')->default(true);
            $table->string('color', 20)->default('#3B82F6');
            $table->timestamps();
        });

        Schema::table('funciones', function (Blueprint $table) {
            if (!Schema::hasColumn('funciones', 'tarifa_id')) {
                $table->foreignId('tarifa_id')
                    ->nullable()
                    ->constrained('tarifas')
                    ->after('precio_base');
            }
        });
    }

    public function down(): void
    {
        Schema::table('funciones', function (Blueprint $table) {
            if (Schema::hasColumn('funciones', 'tarifa_id')) {
                $table->dropForeign(['tarifa_id']);
                $table->dropColumn('tarifa_id');
            }
        });
        Schema::dropIfExists('tarifas');
    }
};
