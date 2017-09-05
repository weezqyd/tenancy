<?php

namespace Elimuswift\Tenancy\Providers;

use Elimuswift\Tenancy\Listeners\Servant;
use Illuminate\Support\ServiceProvider;

class WebserverProvider extends ServiceProvider
{
    public function register()
    {
        // Sets file access as wide as possible, ignoring server masks.
        umask(0);
        $this->registerConfiguration();
        $this->registerGeneratorViews();

        $this->app->register(Webserver\FilesystemProvider::class);
        $this->app->register(Webserver\EventProvider::class);

        $this->app->singleton(Servant::class);
    }

    protected function registerConfiguration()
    {
        $this->publishes([
            __DIR__ . '/../../assets/configs/webserver.php' => config_path('webserver.php'),
        ], 'tenancy');
        $this->mergeConfigFrom(
            __DIR__ . '/../../assets/configs/webserver.php',
            'webserver'
        );
    }

    protected function registerGeneratorViews()
    {
        $this->loadViewsFrom(
            __DIR__ . '/../../assets/generators',
            'tenancy.generator'
        );
    }
}
