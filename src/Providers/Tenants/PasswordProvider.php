<?php

namespace Elimuswift\Tenancy\Providers\Tenants;

use Elimuswift\Tenancy\Contracts\Database\PasswordGenerator;
use Elimuswift\Tenancy\Exceptions\GeneratorInvalidException;
use Illuminate\Support\ServiceProvider;

class PasswordProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PasswordGenerator::class, function ($app) {
            $generator = $app['config']->get('tenancy.db.password-generator');

            if (class_exists($generator)) {
                return $app->make($generator);
            }

            throw new GeneratorInvalidException($generator);
        });
    }

    public function provides()
    {
        return [
            PasswordGenerator::class,
        ];
    }
}
