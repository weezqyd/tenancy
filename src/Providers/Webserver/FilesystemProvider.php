<?php

namespace Elimuswift\Tenancy\Providers\Webserver;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class FilesystemProvider extends ServiceProvider
{
    public function register()
    {
        $this->addDisks();
    }

    protected function addDisks()
    {
        collect(config('webserver', []))
            ->filter(function (array $config) {
                return Arr::has($config, 'generator');
            })
            ->keys()
            ->each(function (string $service) {
                $this->app['config']->set("filesystems.disks.tenancy-webserver-$service", [
                    'driver' => 'local',
                    'root' => storage_path("app/tenancy/webserver/$service"),
                ]);
            });
    }
}
