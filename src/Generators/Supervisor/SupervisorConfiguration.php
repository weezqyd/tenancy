<?php

namespace Elimuswift\Tenancy\Generators\Supervisor;

use Illuminate\Support\Arr;
use Elimuswift\Tenancy\Models\Website;
use Illuminate\Contracts\Events\Dispatcher;
use Elimuswift\Tenancy\Events\Websites as Events;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Create a configuration file for supervisor.
 *
 * @author The Weezqyd <wizqydy@gmail.com>
 **/
class SupervisorConfiguration
{
    /**
     * Subscribe to events.
     *
     * @param Dispatcher
     **/
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Events\Created::class, [$this, 'create']);
        $events->listen(Events\Updated::class, [$this, 'update']);
        $events->listen(Events\Deleted::class, [$this, 'delete']);
    }

    /**
     * Set tenant object.
     *
     * @param Tenant $tenant
     **/
    protected function generate(Website $website)
    {
        $configs = view('tenancy.generator::supervisor.queue-worker', compact('website'));
        $logDir = storage_path("app/tenancy/{$website->uuid}/logs");
        if (!is_dir($logDir)) {
            app('tenant.disk')->makeDirectory("{$website->uuid}/logs");
        }

        return $this->filesystem()->put($this->configFileName($website->uuid), $configs);
    }

    /**
     * Create Configuration and save to file.
     *
     * @param Events\Created $event Website crreated event
     **/
    public function create(Events\Created $event)
    {
        return $this->generate($event->website);
    }

    /**
     * @return Filesystem
     */
    protected function filesystem(): Filesystem
    {
        return app('filesystem.disk');
    }

    /**
     * Get absolute file name and path.
     *
     * @return string
     **/
    protected function configFileName($uuid)
    {
        $path = rtrim(config('webserver.supervisor.config-path'), '/');

        return "{$path}/{$uuid}.conf";
    }

    /**
     * Delete supervisor config file.
     *
     * @param Events\Deleted $event
     **/
    public function delete(Events\Deleted $event)
    {
        $file = $this->configFileName($event->website->uuid);
        if (file_exists($file)) {
            $this->filesystem()->delete($file);
        }
    }

    /**
     * Delete supervisor config file.
     *
     * @param Events\Deleted $event
     **/
    public function update(Events\Updated $event)
    {
        $uuid = Arr::get($event->dirty, 'uuid');
        $file = $this->configFileName($uuid);
        if (file_exists($file)) {
            $this->filesystem()->delete($file);
        }

        return $this->generate($event->website);
    }
}
