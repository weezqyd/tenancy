<?php

namespace Elimuswift\Tenancy\Listeners\Filesystem;

use Elimuswift\Tenancy\Abstracts\AbstractTenantDirectoryListener;
use Elimuswift\Tenancy\Events\Hostnames\Identified;

class LoadsConfigs extends AbstractTenantDirectoryListener
{
    protected $configBaseKey = 'tenancy.folders.config';

    /**
     * @var string
     */
    protected $path = 'config';

    /**
     * @param Identified $event
     */
    public function load(Identified $event)
    {
        $this->readConfigurationFiles($this->path);
    }

    /**
     * @param string $path
     */
    protected function readConfigurationFiles(string $path)
    {
        foreach ($this->directory->files($path) as $file) {
            $key = basename($file, '.php');

            // Blacklisted; skip.
            if (in_array($key, $this->config->get('tenancy.folders.config.blacklist', []))) {
                continue;
            }

            if ($this->directory->isLocal()) {
                $values = $this->directory->getRequire($file);
            } else {
                $values = include 'data:text/plain,' . $this->directory->get($file);
            }

            $existing = $this->config->get($key, []);

            $this->config->set($key, array_merge_recursive(
                $existing,
                $values
            ));
        }
    }
}
