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
        if ($this->app->runningInConsole()) {
            // Register config for publishing
            $this->publishes([
                __DIR__.'/../../config/common-auth.php' => config_path('common-auth.php'),
            ], 'common-auth-config');

            // Register migrations for publishing
            $this->publishes([
                __DIR__.'/../../database/migrations/' => database_path('migrations')
            ], 'common-auth-migrations');

            // Register Artisan commands
            $this->commands([
                \Arjunyuvanesh\CommonAuth\Console\InstallCommand::class,
                \Arjunyuvanesh\CommonAuth\Console\SeedRolesCommand::class,
            ]);
        }

        // Load migrations automatically
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        
        // Load web routes
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }
}
