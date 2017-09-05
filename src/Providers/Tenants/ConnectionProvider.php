<?php

namespace Elimuswift\Tenancy\Providers\Tenants;

use Elimuswift\Tenancy\Database\Connection;
use Illuminate\Support\ServiceProvider;
use Elimuswift\Tenancy\Database\Console\MigrateCommand;
use Illuminate\Contracts\Foundation\Application;

class ConnectionProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Connection::class);

        $this->registerMigrateCommand();
    }

    /**
     * Register the "migrate" migration command.
     */
    protected function registerMigrateCommand()
    {
        $this->app->extend('command.migrate', function ($abstract, Application $app) {
            return new MigrateCommand($app->make('migrator'));
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            Connection::class,
            'command.migrate',
        ];
    }
}
