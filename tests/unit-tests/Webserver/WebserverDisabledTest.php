<?php

namespace Elimuswift\Tenancy\Tests\Webserver;

use Elimuswift\Tenancy\Providers\TenancyProvider;
use Elimuswift\Tenancy\Providers\WebserverProvider;
use Elimuswift\Tenancy\Tests\Test;
use Illuminate\Support\Arr;

class WebserverDisabledTest extends Test
{
    protected $loadProviders = [
        TenancyProvider::class,
    ];

    /**
     * @test
     */
    public function webserver_provider_is_disabled()
    {
        $this->assertFalse(Arr::get($this->app->getLoadedProviders(), WebserverProvider::class, false));
    }
}
