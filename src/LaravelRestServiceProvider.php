<?php

namespace Alirah\LaravelRest;

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
    }
}
