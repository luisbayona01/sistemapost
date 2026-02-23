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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained();
            $table->foreignId('venta_id')->constrained('ventas');
            $table->foreignId('user_id')->constrained('users'); // Cajero/Supervisor
            $table->decimal('monto_total', 12, 2);
            $table->text('motivo');
            $table->boolean('reintegrar_inventario')->default(true);
            $table->string('metodo_pago_devolucion')->default('EFECTIVO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
