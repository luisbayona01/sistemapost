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
        Schema::table('ventas', function (Blueprint $table) {
            // 1. Numeración interna / Documento equivalente POS
            $table->bigInteger('consecutivo_pos')->nullable()->after('numero_comprobante');

            // 2. Estado Fiscal / Contingencia
            $table->string('estado_fiscal')->default('NORMAL')->after('estado_pago'); // NORMAL, PENDIENTE_DIAN, FALLIDO_DIAN, EXITOSO_DIAN
            $table->boolean('en_contingencia')->default(false)->after('estado_fiscal');

            // 3. Campos preparados para Fase 5 (Factura Electrónica/DIAN)
            $table->string('cufe', 100)->nullable()->after('en_contingencia');
            $table->string('numero_factura_dian', 30)->nullable()->after('cufe');
            $table->timestamp('fecha_factura_dian')->nullable()->after('numero_factura_dian');
            $table->string('qrcode_url')->nullable()->after('fecha_factura_dian');
            $table->string('xml_factura_url')->nullable()->after('qrcode_url');
            $table->string('pdf_factura_url')->nullable()->after('xml_factura_url');
            $table->text('error_dian')->nullable()->after('pdf_factura_url');

            // Index para auditoría
            $table->index(['consecutivo_pos', 'caja_id']);
            $table->index('estado_fiscal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'consecutivo_pos',
                'estado_fiscal',
                'en_contingencia',
                'cufe',
                'numero_factura_dian',
                'fecha_factura_dian',
                'qrcode_url',
                'xml_factura_url',
                'pdf_factura_url',
                'error_dian'
            ]);
        });
    }
};
