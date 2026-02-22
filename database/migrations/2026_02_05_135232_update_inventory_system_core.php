<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // 1. Ampliar Empresa con parámetros financieros
        Schema::table('empresa', function (Blueprint $table) {
            $table->decimal('gastos_indirectos_porcentaje', 5, 2)->default(0)->after('porcentaje_impuesto');
            $table->decimal('merma_esperada_porcentaje', 5, 2)->default(0)->after('gastos_indirectos_porcentaje');
        });

        // 2. Gestión de Lotes (FIFO)
        Schema::create('insumo_lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->constrained()->cascadeOnDelete();
            $table->string('numero_lote')->nullable();
            $table->decimal('cantidad_inicial', 15, 3);
            $table->decimal('cantidad_actual', 15, 3);
            $table->decimal('costo_unitario', 15, 2);
            $table->date('fecha_vencimiento')->nullable();
            $table->timestamps();
        });

        // 3. Salidas Especiales (Bajas, Cortesías)
        Schema::create('insumo_salidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('cantidad', 15, 3);
            $table->enum('tipo', ['baja', 'cortesia', 'merma', 'ajuste_inventario']);
            $table->string('motivo')->nullable();
            $table->decimal('costo_estimado', 15, 2)->default(0);
            $table->timestamps();
        });

        // 4. Auditorías Ciegas
        Schema::create('auditorias_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresa')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('fecha_auditoria');
            $table->enum('estado', ['abierta', 'finalizada'])->default('abierta');
            $table->decimal('total_diferencia_valor', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('auditoria_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auditoria_id')->constrained('auditorias_inventario')->cascadeOnDelete();
            $table->foreignId('insumo_id')->constrained()->cascadeOnDelete();
            $table->decimal('stock_teorico', 15, 3);
            $table->decimal('stock_fisico', 15, 3)->nullable();
            $table->decimal('diferencia', 15, 3)->nullable();
            $table->decimal('valor_diferencia', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('auditoria_detalles');
        Schema::dropIfExists('auditorias_inventario');
        Schema::dropIfExists('insumo_salidas');
        Schema::dropIfExists('insumo_lotes');
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropColumn(['gastos_indirectos_porcentaje', 'merma_esperada_porcentaje']);
        });
    }
};
