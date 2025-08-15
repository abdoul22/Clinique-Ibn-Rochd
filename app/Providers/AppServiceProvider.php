<?php

namespace App\Providers;

use Illuminate\Support\Facades\Redis;
use App\Http\Middleware\IsApproved;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Forcer la locale française
        app()->setLocale('fr');

        // Configuration de la pagination pour utiliser Tailwind par défaut
        Paginator::defaultView('pagination::tailwind');
        Paginator::defaultSimpleView('pagination::simple-tailwind');

        // Enregistrement global du middleware 'is.approved'
        Route::middlewareGroup('web', [
            IsApproved::class, // Ceci va l'ajouter aux routes web (facultatif si tu veux tout protéger automatiquement)
        ]);

        Route::middleware('is.approved', IsApproved::class); // Ceci enregistre le middleware

        Redis::enableEvents();
        //Tailwind Pagination
        Paginator::useTailwind();

        // Configuration spéciale pour les données médicales
        config([
            'database.redis.options.prefix' => env('APP_NAME') . ':medical:'
        ]);

        if ($this->app->runningInConsole() && !app()->environment('testing')) {
            $this->commands([
                \App\Console\Commands\GenerateRoomDayCharges::class,
            ]);
        }
    }
}
