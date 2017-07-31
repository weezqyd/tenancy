<?php

namespace Elimuswift\Tenancy\Providers\Tenants;

use Elimuswift\Tenancy\Contracts\Bus\Dispatcher as DispatcherContract;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\ServiceProvider;

class BusProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DispatcherContract::class, function ($app) {
            return new Dispatcher($app);
        });
    }

    public function provides()
    {
        return [
            DispatcherContract::class,
        ];
    }
}
