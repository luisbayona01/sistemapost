<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\Producto;
use App\Observers\ProductoObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // ── PILAR 6.1: MOTOR DE REGLAS ────────────────────────────────
        $this->app->singleton(
            \App\Modules\Core\Contracts\RuleEngineInterface::class,
            \App\Services\RuleEngineService::class
        );

        // 🚀 FASE 7.2: FEATURE FLAGS
        $this->app->singleton(\App\Services\FeatureService::class, function ($app) {
            return new \App\Services\FeatureService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        \App\Models\Producto::observe(\App\Observers\ProductoObserver::class);
        \App\Models\Venta::observe(\App\Observers\MoneyRoundingObserver::class);
        \App\Models\DocumentoFiscal::observe(\App\Observers\MoneyRoundingObserver::class);

        // Directiva Blade para dinero
        \Illuminate\Support\Facades\Blade::directive('currency', function ($expression) {
            return "<?php echo \App\Services\MoneyService::formatCOP($expression); ?>";
        });

        // Directiva @feature para SaaS
        \Illuminate\Support\Facades\Blade::if('feature', function ($key) {
            return app(\App\Services\FeatureService::class)->enabled($key);
        });
    }
}
