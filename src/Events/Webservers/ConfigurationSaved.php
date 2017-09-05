<?php

namespace Elimuswift\Tenancy\Events\Webservers;

use Elimuswift\Tenancy\Abstracts\WebserverEvent;

class ConfigurationSaved extends WebserverEvent
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $configuration;

    /**
     * @param mixed $configuration
     *
     * @return ConfigurationSaved
     */
    public function setConfiguration(string $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @param mixed $path
     *
     * @return ConfigurationSaved
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }
}
