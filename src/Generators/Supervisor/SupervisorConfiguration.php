<?php

namespace Elimuswift\Tenancy\Generators\Supervisor;

use Illuminate\Support\Arr;
use Elimuswift\Tenancy\Models\Website;
use Illuminate\Contracts\Events\Dispatcher;
use Elimuswift\Tenancy\Events\Websites as Events;
use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
            $path = "{$website->uuid}/logs";
            app('tenant.disk')->makeDirectory($path);
            app('tenant.disk')->put(rtrim($path, '/').'/supervisor.log', '');
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
        $this->generate($event->website);
        $this->runScripts();
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
        $this->deleteFile($event->website->uuid);
        $this->runScripts();
    }

    /**
     * Update supervisor config file.
     *
     * @param Events\Updated $event
     **/
    public function update(Events\Updated $event)
    {
        $uuid = Arr::get($event->dirty, 'uuid');
        $this->deleteFile($uuid);
        $this->generate($event->website);
        $this->runScripts();
    }

    /**
     * Delete the supervisor config file from the filesystem.
     *
     * @param string $uuid
     **/
    protected function deleteFile($uuid)
    {
        $file = $this->configFileName($uuid);
        if (file_exists($file)) {
            $this->filesystem()->delete($file);
        }
    }

    /**
     * Run supervisor commands.
     **/
    private function runScripts()
    {
        $commands = [
                        'supervisorctl reread',
                        'supervisorctl update',
                        'supervisorctl restart all',
                    ];
        foreach ($commands as $command) {
            $process = new Process($command);
            try {
                $process->mustRun();
                echo $process->getOutput();
            } catch (ProcessFailedException $e) {
                echo $e->getMessage();
            }
        }
    }
}
