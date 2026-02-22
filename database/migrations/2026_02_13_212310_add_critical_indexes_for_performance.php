<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FASE 4.1: Índices Críticos para Performance
 * 
 * Esta migración agrega índices estratégicos en las tablas más consultadas
 * del sistema para mejorar significativamente el rendimiento de queries,
 * especialmente en reportes y consultas multiempresa.
 * 
 * Impacto esperado: 10-50x más rápido en queries de reportes
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Índice para queries de reportes por empresa y usuario
            $table->index(['empresa_id', 'user_id', 'created_at'], 'idx_ventas_empresa_user_fecha');

            // Índice para búsquedas por fecha (reportes diarios/mensuales)
            $table->index(['empresa_id', 'fecha_hora'], 'idx_ventas_empresa_fecha');

            // Índice para queries por estado de pago
            $table->index(['empresa_id', 'estado_pago'], 'idx_ventas_empresa_estado');

            // Índice para queries por canal (ventanilla, confiteria, web)
            $table->index(['empresa_id', 'canal'], 'idx_ventas_empresa_canal');
        });

        Schema::table('cajas', function (Blueprint $table) {
            // Índice para verificar cajas abiertas por usuario
            $table->index(['empresa_id', 'user_id', 'estado'], 'idx_cajas_empresa_user_estado');

            // Índice para reportes de cajas por fecha
            $table->index(['empresa_id', 'created_at'], 'idx_cajas_empresa_fecha');
        });

        Schema::table('movimientos', function (Blueprint $table) {
            // Índice para queries de movimientos por caja
            $table->index(['caja_id', 'tipo', 'created_at'], 'idx_movimientos_caja_tipo_fecha');

            // Índice para reportes de movimientos
            $table->index(['caja_id', 'created_at'], 'idx_movimientos_caja_fecha');
        });

        Schema::table('inventario', function (Blueprint $table) {
            // Índice para queries de inventario por producto y empresa
            $table->index(['producto_id', 'empresa_id'], 'idx_inventario_producto_empresa');

            // Índice para alertas de stock bajo
            $table->index(['empresa_id', 'cantidad'], 'idx_inventario_empresa_cantidad');
        });

        Schema::table('kardex', function (Blueprint $table) {
            // Índice para historial de movimientos por producto
            $table->index(['producto_id', 'created_at'], 'idx_kardex_producto_fecha');

            // Índice para queries por tipo de transacción
            $table->index(['producto_id', 'tipo_transaccion'], 'idx_kardex_producto_tipo');
        });

        Schema::table('compras', function (Blueprint $table) {
            // Índice para reportes de compras por empresa
            $table->index(['empresa_id', 'created_at'], 'idx_compras_empresa_fecha');

            // Índice para queries por proveedor
            $table->index(['empresa_id', 'proveedore_id'], 'idx_compras_empresa_proveedor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex('idx_ventas_empresa_user_fecha');
            $table->dropIndex('idx_ventas_empresa_fecha');
            $table->dropIndex('idx_ventas_empresa_estado');
            $table->dropIndex('idx_ventas_empresa_canal');
        });

        Schema::table('cajas', function (Blueprint $table) {
            $table->dropIndex('idx_cajas_empresa_user_estado');
            $table->dropIndex('idx_cajas_empresa_fecha');
        });

        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropIndex('idx_movimientos_caja_tipo_fecha');
            $table->dropIndex('idx_movimientos_caja_fecha');
        });

        Schema::table('inventario', function (Blueprint $table) {
            $table->dropIndex('idx_inventario_producto_empresa');
            $table->dropIndex('idx_inventario_empresa_cantidad');
        });

        Schema::table('kardex', function (Blueprint $table) {
            $table->dropIndex('idx_kardex_producto_fecha');
            $table->dropIndex('idx_kardex_producto_tipo');
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->dropIndex('idx_compras_empresa_fecha');
            $table->dropIndex('idx_compras_empresa_proveedor');
        });
    }
};
