<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Assets\Js;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     * TODO: Remove this and add $fillable to all properties
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Js::make('schoolconvoy', __DIR__ . '/../../resources/js/schoolconvoy.js'),
        ]);

        Model::unguard();
    }
}
