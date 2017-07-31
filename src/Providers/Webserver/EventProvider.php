<?php

namespace Elimuswift\Tenancy\Providers\Webserver;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Elimuswift\Tenancy\Listeners;

class EventProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $subscribe = [
        Listeners\Servant::class,
    ];

    public function boot()
    {
        foreach ($this->subscribe as $listener) {
            $this->app[Dispatcher::class]->subscribe($listener);
        }
    }

    public function register()
    {
        // ..
    }
}
