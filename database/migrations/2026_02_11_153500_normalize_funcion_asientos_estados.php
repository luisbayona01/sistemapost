<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * CRÍTICO: Normaliza los estados de asientos a 3 estados claros:
     * - DISPONIBLE: Asiento libre
     * - RESERVADO: Asiento bloqueado temporalmente (5 min)
     * - VENDIDO: Asiento vendido (permanente)
     */
    public function up(): void
    {
        // 1. Convertir temporalmente a VARCHAR para poder migrar datos sin restricciones
        DB::statement("ALTER TABLE funcion_asientos MODIFY COLUMN estado VARCHAR(50) NOT NULL DEFAULT 'DISPONIBLE'");

        // 2. Normalizar todos los datos a mayúsculas y estados correctos
        DB::statement("
            UPDATE funcion_asientos 
            SET estado = CASE 
                WHEN UPPER(estado) IN ('DISPONIBLE') THEN 'DISPONIBLE'
                WHEN UPPER(estado) IN ('BLOQUEADO', 'RESERVADO_TEMPORAL', 'RESERVADO') THEN 'RESERVADO'
                WHEN UPPER(estado) IN ('VENDIDO', 'OCUPADO') THEN 'VENDIDO'
                ELSE 'DISPONIBLE'
            END
        ");

        // 3. Ahora convertir a ENUM con los 3 estados finales
        DB::statement("
            ALTER TABLE funcion_asientos 
            MODIFY COLUMN estado ENUM('DISPONIBLE', 'RESERVADO', 'VENDIDO') 
            NOT NULL DEFAULT 'DISPONIBLE'
        ");

        // 4. Renombrar columnas para claridad
        Schema::table('funcion_asientos', function (Blueprint $table) {
            // Renombrar bloqueado_hasta -> reservado_hasta (más semántico)
            // Solo si bloqueado_hasta existe y reservado_hasta NO existe
            $hasBloqueadoHasta = Schema::hasColumn('funcion_asientos', 'bloqueado_hasta');
            $hasReservadoHasta = Schema::hasColumn('funcion_asientos', 'reservado_hasta');

            if ($hasBloqueadoHasta && !$hasReservadoHasta) {
                $table->renameColumn('bloqueado_hasta', 'reservado_hasta');
            } elseif ($hasBloqueadoHasta && $hasReservadoHasta) {
                // Ambas existen, eliminar la vieja
                $table->dropColumn('bloqueado_hasta');
            }

            // Asegurar que reservado_por existe (ya debería existir de migración anterior)
            if (!Schema::hasColumn('funcion_asientos', 'reservado_por')) {
                $table->foreignId('reservado_por')->nullable()->constrained('users')->after('estado');
            }
        });

        // 5. Agregar índices para performance (usando raw SQL para evitar duplicados)
        try {
            DB::statement('CREATE INDEX idx_funcion_estado ON funcion_asientos (funcion_id, estado)');
        } catch (\Exception $e) {
            // Índice ya existe, ignorar
        }

        try {
            DB::statement('CREATE INDEX idx_reservado_hasta ON funcion_asientos (reservado_hasta)');
        } catch (\Exception $e) {
            // Índice ya existe, ignorar
        }


        // 6. Limpiar reservas huérfanas existentes (más de 5 minutos)
        DB::statement("
            UPDATE funcion_asientos 
            SET estado = 'DISPONIBLE', 
                reservado_hasta = NULL, 
                session_id = NULL,
                reservado_por = NULL
            WHERE estado = 'RESERVADO' 
            AND reservado_hasta < NOW() - INTERVAL 5 MINUTE
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funcion_asientos', function (Blueprint $table) {
            $table->dropIndex('idx_funcion_estado');
            $table->dropIndex('idx_reservado_hasta');

            if (Schema::hasColumn('funcion_asientos', 'reservado_hasta')) {
                $table->renameColumn('reservado_hasta', 'bloqueado_hasta');
            }
        });

        // Volver al estado anterior
        DB::statement("
            ALTER TABLE funcion_asientos 
            MODIFY COLUMN estado ENUM('DISPONIBLE', 'RESERVADO_TEMPORAL', 'VENDIDO', 'BLOQUEADO', 'OCUPADO') 
            NOT NULL DEFAULT 'DISPONIBLE'
        ");
    }
};
