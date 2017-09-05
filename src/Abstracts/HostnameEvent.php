<?php

namespace Elimuswift\Tenancy\Abstracts;

use Elimuswift\Tenancy\Models\Hostname;

abstract class HostnameEvent extends AbstractEvent
{
    /**
     * @var Hostname
     */
    public $hostname;

    public function __construct(Hostname &$hostname = null)
    {
        $this->hostname = &$hostname;
    }
}
