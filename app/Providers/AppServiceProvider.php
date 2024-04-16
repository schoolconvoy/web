<?php

namespace App\Providers;

use App\Models\Scopes\ClassScope;
use App\Models\User;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Opcodes\LogViewer\Facades\LogViewer;
use Illuminate\Support\Facades\Log;

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
        User::addGlobalScope(new ClassScope);

        LogViewer::auth(function ($request) {
            return $request->user()
                && in_array($request->user()->email, [
                    'olaegbesamuel@gmail.com',
                ]);
        });

        FilamentView::registerRenderHook(
            'panels::user-menu.after',
            fn (): string => auth()->user()->hasRole(User::$PARENT_ROLE) ? Blade::render('@livewire(\'ward-switcher\')') : '',
        );

        FilamentAsset::register([
            Js::make('schoolconvoy', __DIR__ . '/../../resources/js/schoolconvoy.js'),
        ]);

        Model::unguard();
    }
}
