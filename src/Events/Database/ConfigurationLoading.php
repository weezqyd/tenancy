<?php

namespace Elimuswift\Tenancy\Events\Database;

use Elimuswift\Tenancy\Abstracts\AbstractEvent;
use Elimuswift\Tenancy\Database\Connection;

class ConfigurationLoading extends AbstractEvent
{
    /**
     * @var string
     */
    public $mode;

    /**
     * @var array
     */
    public $configuration;

    /**
     * @var Connection
     */
    public $connection;

    /**
     * @param string     $mode
     * @param array      $configuration
     * @param Connection $connection
     */
    public function __construct(string &$mode, array &$configuration, Connection &$connection)
    {
        $this->mode = &$mode;
        $this->configuration = &$configuration;
        $this->connection = &$connection;
    }
}
