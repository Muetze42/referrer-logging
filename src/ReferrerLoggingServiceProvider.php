<?php

namespace NormanHuth\ReferrerLogging;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use NormanHuth\ReferrerLogging\Commands\CleanupFilesystem;

class ReferrerLoggingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/referrer-logging.php' => config_path('referrer-logging.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');


        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupFilesystem::class
            ]);
        }
    }
}
