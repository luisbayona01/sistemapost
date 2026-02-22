<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FiscalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Interfaces\FiscalProviderInterface::class, function ($app) {
            $provider = config('fiscal.proveedor_activo', 'alegra');

            return match ($provider) {
                'alegra' => new \App\Services\Fiscal\Providers\AlegraProvider(),
                default => new \App\Services\Fiscal\Providers\NullFiscalProvider(),
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
