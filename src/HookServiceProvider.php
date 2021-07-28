<?php

declare(strict_types=1);

namespace Esemve\Hook;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->commands([
            Console\HookListeners::class,
        ]);

        $this->app->singleton(Hook::class, function (Application $app) {
            return new Hook();
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [Hook::class, Console\HookListeners::class];
    }
}
