<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── TABLA PRINCIPAL: REGLAS ──────────────────────────────────────────
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('event_type', 100)->index();   // 'stock.low', 'sala.high_occupancy', 'caja.sale', etc.
            $table->string('logical_operator', 3)->default('AND'); // AND | OR
            $table->unsignedTinyInteger('priority')->default(50);  // 1=alta ... 100=baja
            $table->boolean('active')->default(true);
            $table->boolean('stop_on_match')->default(false);      // detener evaluación si coincide
            $table->unsignedInteger('execution_count')->default(0);
            $table->timestamp('last_executed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['empresa_id', 'event_type', 'active']);
        });

        // ── CONDICIONES ──────────────────────────────────────────────────────
        Schema::create('rule_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('rules')->cascadeOnDelete();
            $table->string('field', 120);          // campo que se evalúa. ej: 'stock', 'occupancy_pct', 'total_venta'
            $table->string('operator', 20);        // '>', '<', '>=', '<=', '==', '!=', 'in', 'not_in', 'contains'
            $table->string('value', 255);          // valor de referencia (serializado si es array)
            $table->string('data_type', 20)->default('numeric'); // numeric | string | boolean | array
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ── ACCIONES ─────────────────────────────────────────────────────────
        Schema::create('rule_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('rules')->cascadeOnDelete();
            $table->string('action_type', 100);    // 'alert', 'price_adjustment', 'flag', 'notification', 'webhook', 'upsell'
            $table->json('parameters')->nullable(); // config de la acción (monto, %, mensaje, etc.)
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // ── LOG / AUDITORÍA DE EJECUCIONES ───────────────────────────────────
        Schema::create('rule_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('rules')->cascadeOnDelete();
            $table->unsignedBigInteger('empresa_id')->index();
            $table->string('event_type', 100);
            $table->json('context')->nullable();          // datos del evento que disparó la regla
            $table->json('conditions_result')->nullable();// resultado booleano por condición
            $table->json('actions_result')->nullable();   // resultado de cada acción ejecutada
            $table->boolean('matched')->default(false);   // ¿La regla coincidió?
            $table->boolean('executed')->default(false);  // ¿Las acciones se ejecutaron?
            $table->text('error_message')->nullable();
            $table->unsignedSmallInteger('execution_time_ms')->nullable();
            $table->timestamp('executed_at')->useCurrent();
            $table->index(['rule_id', 'executed_at']);
            $table->index(['empresa_id', 'event_type', 'executed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_executions');
        Schema::dropIfExists('rule_actions');
        Schema::dropIfExists('rule_conditions');
        Schema::dropIfExists('rules');
    }
};
