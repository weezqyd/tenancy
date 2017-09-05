<?php

namespace Elimuswift\Tenancy\Events\Hostnames;

use Elimuswift\Tenancy\Abstracts\HostnameEvent;
use Elimuswift\Tenancy\Models\Hostname;

class Updated extends HostnameEvent
{
    /**
     * @var array
     */
    public $dirty;

    public function __construct(Hostname $hostname = null, array $dirty = [])
    {
        parent::__construct($hostname);

        $this->dirty = $dirty;
    }
}
