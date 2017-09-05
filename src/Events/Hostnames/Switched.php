<?php

namespace Elimuswift\Tenancy\Events\Hostnames;

use Elimuswift\Tenancy\Abstracts\HostnameEvent;
use Elimuswift\Tenancy\Models\Hostname;

class Switched extends HostnameEvent
{
    /**
     * @var Hostname
     */
    public $old;

    /**
     * @param Hostname $hostname
     *
     * @return $this
     */
    public function setOld(Hostname $hostname)
    {
        $this->old = $hostname;

        return $this;
    }
}
