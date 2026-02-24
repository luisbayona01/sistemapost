<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('inventory:restock-alert')->weeklyOn(1, '08:00');

        // Expirar ventas web pendientes cada 15 minutos
        $schedule->job(new \App\Jobs\ExpireStaleWebSales)
            ->everyFifteenMinutes()
            ->withoutOverlapping();

        // Validación de integridad de inventario diaria
        $schedule->command('app:validate-inventory-integrity')
            ->dailyAt('02:00')
            ->withoutOverlapping();

        // 🎬 CRÍTICO: Liberar asientos (Ghost Seats) - Hardening Fase 4
        $schedule->command('cinema:clean-seats')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/cinema-clean.log'));

        // 🧠 MÓDULO 3: Generar Alertas de Inteligencia Operativa
        $schedule->job(new \App\Jobs\GenerarAlertasAutomaticas)
            ->hourly()
            ->withoutOverlapping();

        // Procesar contingencias fiscales cada 15 minutos (Fase 5)
        $schedule->command('fiscal:procesar-contingencias')
            ->everyFifteenMinutes()
            ->withoutOverlapping();

        // 🏥 SALUD MULTI-TENANT: Verificar integridad cada lunes 8 AM
        $schedule->command('health:multi-tenant')
            ->weekly()
            ->mondays()
            ->at('08:00')
            ->appendOutputTo(storage_path('logs/multi-tenant-health.log'));

        // 💾 COPIAS DE SEGURIDAD (Fase 7.5 - Tenant-Aware)
        $schedule->command('backup:tenant')
            ->dailyAt('03:00')
            ->withoutOverlapping();

        $schedule->command('backup:run --only-files')
            ->weeklyOn(0, '03:30')
            ->withoutOverlapping();

        $schedule->command('backup:clean')
            ->dailyAt('04:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
