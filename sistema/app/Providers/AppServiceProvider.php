<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        // Permiso por módulo según el rol del usuario.
        // En rutas: ->middleware('can:modulo,"pedidos"') (entre comillas para que sea texto)   En vistas: @can('modulo', 'pedidos')
        Gate::define('modulo', fn (User $user, string $modulo) => $user->puede($modulo));
    }
}
