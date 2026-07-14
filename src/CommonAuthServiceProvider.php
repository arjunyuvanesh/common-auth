<?php

namespace Arjunyuvanesh\CommonAuth;

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
        $this->mergeConfigFrom(
            __DIR__.'/../config/common-auth.php', 'common-auth'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/common-auth.php' => config_path('common-auth.php'),
            ], 'common-auth-config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations')
            ], 'common-auth-migrations');

            $this->commands([
                \Arjunyuvanesh\CommonAuth\Commands\InstallCommand::class,
            ]);
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
