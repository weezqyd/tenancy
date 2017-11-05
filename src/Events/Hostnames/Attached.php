<?php

namespace Elimuswift\Tenancy\Events\Hostnames;

use Elimuswift\Tenancy\Abstracts\AbstractEvent;
use Elimuswift\Tenancy\Models\Hostname;
use Elimuswift\Tenancy\Models\Website;

class Attached extends AbstractEvent
{
    /**
     * @var Hostname
     */
    public $hostname;
    /**
     * @var Website
     */
    public $website;

    public function __construct(Hostname $hostname, Website $website)
    {
        $this->hostname = $hostname;
        $this->website = $website;
    }
}
