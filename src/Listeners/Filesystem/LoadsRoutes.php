<?php

namespace Elimuswift\Tenancy\Listeners\Filesystem;

use Elimuswift\Tenancy\Abstracts\AbstractTenantDirectoryListener;
use Elimuswift\Tenancy\Events\Hostnames\Identified;
use Elimuswift\Tenancy\Exceptions\FilesystemException;
use Illuminate\Routing\Router;

class LoadsRoutes extends AbstractTenantDirectoryListener
{
    protected $configBaseKey = 'tenancy.folders.routes';

    /**
     * @var string
     */
    protected $path = 'routes.php';

    /**
     * @param Identified $event
     *
     * @throws FilesystemException
     */
    public function load(Identified $event)
    {
        if ($this->directory->isLocal()) {
            $this->loadRoutes($this->path);
        } else {
            throw new FilesystemException("$this->path is not available locally, cannot include");
        }
    }

    /**
     * @param $path
     */
    public function loadRoutes($path)
    {
        /** @var Router $router */
        $router = app('router');

        $prefix = $this->config->get('tenancy.folders.routes.prefix', '');

        $router->group(
            $prefix ? compact('prefix') : [],
            function ($router) use ($path) {
                return $this->directory->getRequire($path);
            }
        );
    }
}
