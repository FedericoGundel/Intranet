<?php

namespace App\Providers;

use App\Models\ArticuloFactura;
use App\Models\Configuration;
use App\Observers\ArticuloFacturaObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1) Registrar el observer (stock)
        ArticuloFactura::observe(ArticuloFacturaObserver::class);

        // 2) Cargar configuraciones solo si la tabla existe
        try {
            $configurations = Configuration::query()
                ->pluck('value', 'key')
                ->toArray();

            View::share('configurations', $configurations);
        } catch (\Exception $e) {
            // Si la tabla no existe aún, usar array vacío
            View::share('configurations', []);
        }
    }
}
