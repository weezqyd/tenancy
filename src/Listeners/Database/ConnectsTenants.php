<?php

namespace Elimuswift\Tenancy\Listeners\Database;

use Elimuswift\Tenancy\Abstracts\HostnameEvent;
use Elimuswift\Tenancy\Database\Connection;
use Illuminate\Contracts\Events\Dispatcher;
use Elimuswift\Tenancy\Events;

class ConnectsTenants
{
    /**
     * @var Connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Events\Hostnames\Identified::class, [$this, 'switch']);
        $events->listen(Events\Hostnames\Switched::class, [$this, 'switch']);
    }

    /**
     * Reacts to this service when we switch the active tenant website.
     *
     * @param HostnameEvent $event
     *
     * @return bool
     */
    public function switch(HostnameEvent $event): bool
    {
        return $this->connection->set($event->hostname);
    }
}
