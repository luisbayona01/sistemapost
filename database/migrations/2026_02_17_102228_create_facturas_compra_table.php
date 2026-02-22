<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Nueva estructura de facturas de compra (Simplificada y sin fricción)
        Schema::create('facturas_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores');
            $table->string('numero_factura')->nullable();
            $table->date('fecha_compra');
            $table->decimal('total_pagado', 12, 2);
            $table->string('impuesto_tipo')->nullable(); // IVA, Consumo, etc.
            $table->decimal('impuesto_porcentaje', 5, 2)->nullable();
            $table->decimal('impuesto_valor', 12, 2)->default(0);
            $table->decimal('subtotal_calculado', 12, 2);
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        // Tabla de movimientos de inventario (Kardex detallado para facturas)
        Schema::create('inventario_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('factura_id')->constrained('facturas_compra')->onDelete('cascade');
            $table->decimal('cantidad', 12, 2);
            $table->decimal('costo_unitario', 12, 4); // Costo real (sin impuesto)
            $table->string('origen')->default('FACTURA_COMPRA');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
        Schema::dropIfExists('facturas_compra');
    }
};
