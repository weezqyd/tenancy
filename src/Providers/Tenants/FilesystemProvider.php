<?php

namespace Elimuswift\Tenancy\Providers\Tenants;

use Elimuswift\Tenancy\Abstracts\AbstractTenantDirectoryListener;
use Elimuswift\Tenancy\Website\Directory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class FilesystemProvider extends ServiceProvider
{
    public function register()
    {
        $this->addDisks();

        $this->app->singleton('tenant.disk', function ($app) {
            /** @var \Illuminate\Filesystem\FilesystemManager $manager */
            $manager = $app->make('filesystem');

            return $manager->disk($app['config']->get('tenancy.website.disk') ?: 'tenancy-default');
        });

        $this->app->when(Directory::class)
            ->needs(Filesystem::class)
            ->give('tenant.disk');

        $this->app->when(AbstractTenantDirectoryListener::class)
            ->needs(Filesystem::class)
            ->give('tenant.disk');

        $this->app->bind(Directory::class);
    }

    protected function addDisks()
    {
        $this->app['config']->set('filesystems.disks.tenancy-default', [
            'driver' => 'local',
            'root' => storage_path('app/tenancy'),
        ]);
    }
}
