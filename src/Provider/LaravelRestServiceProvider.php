<?php

namespace Alirah\LaravelRest\Provider;

use Alirah\LaravelRest\Commands\CreateRest;
use Alirah\LaravelRest\Commands\DeleteRest;
use Illuminate\Support\ServiceProvider;

class LaravelRestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'laravel-rest');
    }

    /**
     * Register the application services
     *
     * @return void
     */
    public function register()
    {
        // make command
        $this->app->singleton('rest:make', function ($app) {
            return new CreateRest();
        });
        $this->commands([
            'rest:make'
        ]);

        // delete command
        $this->app->singleton('rest:delete', function ($app) {
            return new DeleteRest();
        });
        $this->commands([
            'rest:delete'
        ]);

        // publish config file
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('laravel-rest.php'),
        ], 'config');
    }
}
