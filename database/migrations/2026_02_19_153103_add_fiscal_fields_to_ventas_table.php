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
            // Campos de identificación del cliente (Copia de seguridad si el cliente cambia en el futuro)
            $table->string('cliente_tipo_doc', 10)->default('CC')->after('cliente_id');
            $table->string('cliente_documento', 20)->nullable()->after('cliente_tipo_doc');
            $table->string('cliente_nombre', 200)->nullable()->after('cliente_documento');
            $table->string('cliente_email')->nullable()->after('cliente_nombre');
            $table->string('cliente_telefono', 20)->nullable()->after('cliente_email');

            // Preferencias de Facturación
            $table->boolean('solicita_factura')->default(false)->after('cliente_telefono')
                ->comment('true = Cliente pidió factura explícitamente');
            $table->string('preferencia_fiscal')->default('fe_todo')->after('solicita_factura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'cliente_tipo_doc',
                'cliente_documento',
                'cliente_nombre',
                'cliente_email',
                'cliente_telefono',
                'solicita_factura',
                'preferencia_fiscal'
            ]);
        });
    }
};
