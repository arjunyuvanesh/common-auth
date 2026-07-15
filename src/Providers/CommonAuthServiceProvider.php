<?php

namespace Arjunyuvanesh\CommonAuth\Providers;

use Illuminate\Support\ServiceProvider;

class CommonAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge package configuration with host application configuration
        $this->mergeConfigFrom(
            __DIR__.'/../../config/common-auth.php', 'common-auth'
        );

        // Bind the AuthService interface to its concrete implementation (Dependency Inversion)
        $this->app->bind(
            \Arjunyuvanesh\CommonAuth\Contracts\AuthServiceInterface::class,
            \Arjunyuvanesh\CommonAuth\Services\AuthService::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Enforce mathematically strong passwords for the entire package
        \Illuminate\Validation\Rules\Password::defaults(function () {
            return \Illuminate\Validation\Rules\Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });

        // Register Dynamic Rate Limiter for Brute-Force Protection
        \Illuminate\Support\Facades\RateLimiter::for('common-auth', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(config('common-auth.rate_limit', 6))->by($request->ip());
        });

        // Load translations from the package directory
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'common-auth');

        if ($this->app->runningInConsole()) {
            // Register config for publishing
            $this->publishes([
                __DIR__.'/../../config/common-auth.php' => config_path('common-auth.php'),
            ], 'common-auth-config');

            // Register migrations for publishing
            $this->publishes([
                __DIR__.'/../../database/migrations/' => database_path('migrations')
            ], 'common-auth-migrations');

            // Register translations for publishing
            $this->publishes([
                __DIR__.'/../../resources/lang' => $this->app->langPath('vendor/common-auth'),
            ], 'common-auth-translations');

            // Register Artisan commands
            $this->commands([
                \Arjunyuvanesh\CommonAuth\Console\InstallCommand::class,
                \Arjunyuvanesh\CommonAuth\Console\SeedRolesCommand::class,
            ]);
        }

        // Load migrations automatically
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        
        // Load all routes for both Web Browsers and Mobile Apps
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
    }
}
