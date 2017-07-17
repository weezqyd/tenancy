<?php

/*
 * This file is part of the hyn/multi-tenant package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/hyn/multi-tenant
 *
 */

namespace Hyn\Tenancy\Providers;

use Hyn\Tenancy\Resolver;
use Hyn\Tenancy\Commands\InstallCommand;
use Hyn\Tenancy\Contracts;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Providers\Tenants as Providers;
use Hyn\Tenancy\Repositories;
use Illuminate\Support\ServiceProvider;

class TenancyProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(Providers\ConfigurationProvider::class);
        $this->app->register(Providers\PasswordProvider::class);
        $this->app->register(Providers\ConnectionProvider::class);
        $this->app->register(Providers\UuidProvider::class);
        $this->app->register(Providers\BusProvider::class);
        $this->app->register(Providers\FilesystemProvider::class);

        // Register last.
        $this->app->register(Providers\EventProvider::class);

        $this->installCommand();

        $this->repositories();

        $this->migrations();
        $this->registerConfiguration();

        $this->app->singleton(Resolver::class);
    }

    public function boot()
    {
        // Now register it into ioc to make it globally available.
        $this->app->singleton(Environment::class, function ($app) {
            return new Environment($app);
        });
    }

    protected function installCommand()
    {
        $this->commands(InstallCommand::class);
    }

    protected function repositories()
    {
        $this->app->singleton(
            Contracts\Repositories\HostnameRepository::class,
            Repositories\HostnameRepository::class
        );
        $this->app->singleton(
            Contracts\Repositories\WebsiteRepository::class,
            Repositories\WebsiteRepository::class
        );
        $this->app->singleton(
            Contracts\Repositories\CustomerRepository::class,
            Repositories\CustomerRepository::class
        );
    }

    protected function migrations()
    {
        //$this->loadMigrationsFrom(realpath(__DIR__.'/../../assets/migrations'));
    }

    public function provides()
    {
        return [
            Environment::class,
            InstallCommand::class,
            Contracts\Repositories\HostnameRepository::class,
            Contracts\Repositories\WebsiteRepository::class,
        ];
    }

    protected function registerConfiguration()
    {
        $this->publishes([
            __DIR__.'/../../assets/configs/tenancy.php' => config_path('tenancy.php'),
        ], 'tenancy');
    }
}
