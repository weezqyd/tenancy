<?php

namespace Elimuswift\Tenancy\Generators\Filesystem;

use Illuminate\Contracts\Events\Dispatcher;
use Elimuswift\Tenancy\Events\Websites as Events;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class DirectoryGenerator
{
    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Events\Created::class, [$this, 'created']);
        $events->listen(Events\Updated::class, [$this, 'updated']);
        $events->listen(Events\Deleted::class, [$this, 'deleted']);
    }

    /**
     * @return Filesystem
     */
    protected function filesystem(): Filesystem
    {
        return app('tenant.disk');
    }

    /**
     * Mutates the service based on a website being enabled.
     *
     * @param Events\Created $event
     *
     * @return bool
     */
    public function created(Events\Created $event): bool
    {
        if (config('tenancy.website.auto-create-tenant-directory')) {
            $directories = config('tenancy.directories');
            $uuid = $event->website->uuid;
            $this->filesystem()->makeDirectory("tenants/{$uuid}");
            foreach ($directories as $directory) {
                $this->filesystem()->makeDirectory("tenants/{$uuid}/{$directory}");
            }
        }

        return true;
    }

    /**
     * @param Events\Updated $event
     *
     * @return bool
     */
    public function updated(Events\Updated $event): bool
    {
        $rename = config('tenancy.website.auto-rename-tenant-directory');

        if ($rename && $uuid = Arr::get($event->dirty, 'uuid')) {
            return $this->filesystem()->move(
                $uuid,
                $event->website->uuid
            );
        }

        return true;
    }

    /**
     * Acts on this service whenever a website is disabled.
     *
     * @param Events\Deleted $event
     *
     * @return bool
     */
    public function deleted(Events\Deleted $event): bool
    {
        if (config('tenancy.website.auto-delete-tenant-directory')) {
            return $this->filesystem()->deleteDirectory($event->website->uuid);
        }

        return true;
    }
}
