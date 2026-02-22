<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * FIX Fase 6: Ampliar ENUM 'estado' en documentos_fiscales para incluir
     * los estados asincrónicos de Fase 5 que faltaban:
     * - pendiente_emision    → documento creado, job aún no lo ha procesado
     * - contingencia_permanente → falló definitivamente tras N reintentos
     */
    public function up(): void
    {
        // MySQL no permite ALTER COLUMN en ENUMs directamente con Blueprint,
        // por lo que usamos raw SQL para modificar solo los valores del ENUM.
        DB::statement("
            ALTER TABLE documentos_fiscales
            MODIFY COLUMN estado ENUM(
                'borrador',
                'pendiente_emision',
                'emitido',
                'enviado',
                'aceptado',
                'rechazado',
                'contingencia',
                'contingencia_permanente',
                'enviado_posterior'
            ) NOT NULL DEFAULT 'borrador'
        ");
    }

    public function down(): void
    {
        // Revertir: los registros con estados nuevos quedarán como 'borrador' al rollback
        DB::statement("
            UPDATE documentos_fiscales
            SET estado = 'borrador'
            WHERE estado IN ('pendiente_emision', 'contingencia_permanente')
        ");

        DB::statement("
            ALTER TABLE documentos_fiscales
            MODIFY COLUMN estado ENUM(
                'borrador',
                'emitido',
                'enviado',
                'aceptado',
                'rechazado',
                'contingencia',
                'enviado_posterior'
            ) NOT NULL DEFAULT 'borrador'
        ");
    }
};
