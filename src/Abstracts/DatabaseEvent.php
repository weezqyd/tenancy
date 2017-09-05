<?php
namespace Elimuswift\Tenancy\Abstracts;

use Elimuswift\Tenancy\Models\Website;

abstract class DatabaseEvent extends AbstractEvent
{
    /**
     * @var Website
     */
    public $website;

    /**
     * @var array
     */
    public $config;

    public function __construct(array &$config, Website $website = null)
    {
        $this->config   = $config;
        $this->hostname = &$hostname;
    }
}
