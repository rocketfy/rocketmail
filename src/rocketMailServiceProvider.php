<?php

namespace rocketfy\rocketMail;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class rocketMailServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Route::middlewareGroup('rocketmail', config('rocketmail.middlewares', []));

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'rocketmail');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'rocketmail');
        $this->registerRoutes();

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        });
    }

    /**
     * Get the Telescope route group configuration array.
     *
     * @return array
     */
    private function routeConfiguration()
    {
        return [
            'namespace' => 'rocketfy\rocketMail\Http\Controllers',
            'prefix' => config('rocketmail.path'),
            'middleware' => 'rocketmail',
        ];
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rocketmail.php', 'rocketmail');

        // Register the service the package provides.
        $this->app->singleton('rocketmail', function ($app) {
            return new rocketMail;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['rocketmail'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/rocketmail.php' => config_path('rocketmail.php'),
        ], 'rocketmail.config');

        $this->publishes([
                __DIR__.'/../public' => public_path('vendor/rocketmail'),
            ], 'public');

        $this->publishes([
                __DIR__.'/../resources/views/templates' => $this->app->resourcePath('views/vendor/rocketmail/templates'),
            ], 'rocketmail.templates');
    }
}
