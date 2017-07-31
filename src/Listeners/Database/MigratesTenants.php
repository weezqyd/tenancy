<?php

namespace Elimuswift\Tenancy\Listeners\Database;

use Elimuswift\Tenancy\Abstracts\WebsiteEvent;
use Elimuswift\Tenancy\Database\Connection;
use Illuminate\Contracts\Events\Dispatcher;
use Elimuswift\Tenancy\Events;

class MigratesTenants
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
        $events->listen(Events\Websites\Created::class, [$this, 'migrate']);
    }

    /**
     * @param WebsiteEvent $event
     *
     * @return bool
     */
    public function migrate(WebsiteEvent $event): bool
    {
        if ($path = config('tenancy.db.tenant-migrations-path')) {
            return $this->connection->migrate($event->website, $path);
        }

        return true;
    }
}
