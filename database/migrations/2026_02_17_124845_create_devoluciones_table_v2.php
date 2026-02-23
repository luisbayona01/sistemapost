<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Limpiamos si existen por la migración vieja
        Schema::dropIfExists('devolucion_items');
        Schema::dropIfExists('devoluciones');

        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('venta_id')->constrained('ventas');
            $table->foreignId('user_id')->constrained('users')
                ->comment('Quién procesó la devolución');
            $table->enum('tipo', ['BOLETERIA', 'CONFITERIA', 'MIXTA']);
            $table->decimal('monto_devuelto', 10, 2);
            $table->text('motivo');
            $table->boolean('es_excepcional')->default(false)
                ->comment('true = devolución de días pasados, solo Root');
            $table->text('autorizacion_nota')->nullable()
                ->comment('Nota de autorización para casos excepcionales');
            $table->timestamps();

            $table->index(['empresa_id', 'venta_id']);
        });

        Schema::create('devolucion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolucion_id')
                ->constrained('devoluciones')
                ->onDelete('cascade');
            $table->enum('tipo_item', ['BOLETO', 'PRODUCTO']);
            $table->foreignId('funcion_asiento_id')->nullable()
                ->constrained('funcion_asientos');
            $table->foreignId('producto_id')->nullable()
                ->constrained('productos');
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('monto', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devolucion_items');
        Schema::dropIfExists('devoluciones');
    }
};
