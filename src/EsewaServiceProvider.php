<?php

namespace NikhilPandey\Esewa;

use Illuminate\Support\ServiceProvider;

class EsewaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
             __DIR__.'/../config/esewa.php' => config_path('esewa.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge the configuration files
        $this->mergeConfigFrom(
            __DIR__.'/../config/esewa.php', 'esewa'
        );

        // Bind to the IoC Container
        $this->app->singleton('esewa', function ($app) {
            return new Esewa;
        });
    }
}
