<?php

namespace Elimuswift\Tenancy\Providers;

use Elimuswift\Tenancy\Resolver;
use Elimuswift\Tenancy\Contracts;
use Elimuswift\Tenancy\Environment;
use Elimuswift\Tenancy\Repositories;
use Illuminate\Support\ServiceProvider;
use Elimuswift\Tenancy\Commands\InstallCommand;
use Elimuswift\Tenancy\Commands\TenantsCommand;
use Elimuswift\Tenancy\Commands\RegenerateWokersCommand;
use Elimuswift\Tenancy\Providers\Tenants as Providers;

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

        $this->repositories();
        $this->app->singleton(Resolver::class);
        $this->app->bind('resolver', function ($app) {
            return $app[Resolver::class];
        });
        // Register last in order to listen to events from other modules
        $this->app->register(Providers\EventProvider::class);

        // Now register it into ioc to make it globally available.
        $this->app->singleton(Environment::class, function ($app) {
            return new Environment($app);
        });
        $this->registaerCommands();
        $this->migrations();
    }

    protected function registaerCommands()
    {
        $this->commands(InstallCommand::class);
        $this->commands(TenantsCommand::class);
        $this->commands(RegenerateWokersCommand::class);
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
        $this->publishes([realpath(__DIR__.'/../../assets/migrations') => database_path('tenancy')]);
    }

    public function provides()
    {
        return [
            Resolver::class,
            Environment::class,
            InstallCommand::class,
            Contracts\Repositories\HostnameRepository::class,
            Contracts\Repositories\WebsiteRepository::class,
        ];
    }
}
