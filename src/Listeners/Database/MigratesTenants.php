<?php

/*
 * This file is part of the hyn/multi-tenant package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/hyn/multi-tenant
 *
 */

namespace Hyn\Tenancy\Listeners\Database;

use Hyn\Tenancy\Abstracts\WebsiteEvent;
use Hyn\Tenancy\Database\Connection;
use Illuminate\Contracts\Events\Dispatcher;
use Hyn\Tenancy\Events;

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
