<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabla principal de documentos fiscales
        Schema::create('documentos_fiscales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('venta_id')->nullable()->constrained('ventas');

            // Tipo de documento
            $table->enum('tipo_documento', ['DE', 'FE'])
                ->comment('DE=Documento Equivalente, FE=Factura Electrónica');

            // Numeración
            $table->string('prefijo', 10);
            $table->string('numero', 20);
            $table->string('numero_completo', 30)->unique();

            // Estado del documento
            $table->enum('estado', [
                'borrador',
                'emitido',
                'enviado',
                'aceptado',
                'rechazado',
                'contingencia',
                'enviado_posterior'
            ])->default('borrador');

            // Datos DIAN (para FE)
            $table->string('cufe', 100)->nullable()->comment('Código Único de Factura Electrónica');
            $table->string('cude', 100)->nullable()->comment('Código Único de Documento Equivalente');
            $table->text('qr_code')->nullable();
            $table->text('xml_path')->nullable();
            $table->text('pdf_path')->nullable();

            // Datos del cliente
            $table->string('cliente_tipo_documento', 10)->default('CC');
            $table->string('cliente_documento', 20)->nullable();
            $table->string('cliente_nombre', 200)->default('Consumidor Final');
            $table->string('cliente_email', 100)->nullable();
            $table->string('cliente_telefono', 20)->nullable();
            $table->text('cliente_direccion')->nullable();

            // Totales
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('impuesto_inc', 10, 2)->default(0)
                ->comment('Impuesto Nacional al Consumo 8%');
            $table->decimal('otros_impuestos', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // Respuesta del proveedor
            $table->text('respuesta_proveedor')->nullable();
            $table->text('mensaje_error')->nullable();
            $table->integer('intentos_envio')->default(0);
            $table->timestamp('fecha_emision')->nullable();
            $table->timestamp('fecha_aceptacion_dian')->nullable();

            // Contingencia
            $table->boolean('es_contingencia')->default(false);
            $table->text('motivo_contingencia')->nullable();
            $table->timestamp('fecha_contingencia')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['empresa_id', 'tipo_documento', 'estado']);
            $table->index(['numero_completo']);
            $table->index(['created_at']);
        });

        // Tabla de líneas del documento
        Schema::create('documento_fiscal_lineas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_fiscal_id')
                ->constrained('documentos_fiscales')
                ->onDelete('cascade');

            $table->integer('linea')->default(1);
            $table->enum('tipo_item', ['BOLETO', 'PRODUCTO', 'SERVICIO']);

            $table->string('codigo', 50)->nullable();
            $table->string('descripcion', 500);
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal_linea', 10, 2);

            // Impuestos por línea
            $table->boolean('aplica_inc')->default(false);
            $table->decimal('valor_inc', 10, 2)->default(0);
            $table->decimal('total_linea', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_fiscal_lineas');
        Schema::dropIfExists('documentos_fiscales');
    }
};
