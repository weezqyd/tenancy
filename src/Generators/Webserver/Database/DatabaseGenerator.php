<?php

namespace Elimuswift\Tenancy\Generators\Webserver\Database;

use Elimuswift\Tenancy\Database\Connection;
use Elimuswift\Tenancy\Events;
use Elimuswift\Tenancy\Exceptions\GeneratorFailedException;
use Elimuswift\Tenancy\Traits\DispatchesEvents;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Connection as IlluminateConnection;
use Illuminate\Support\Arr;

class DatabaseGenerator
{
    use DispatchesEvents;
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $mode;

    /**
     * DatabaseGenerator constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->mode = config('tenancy.db.tenant-division-mode');
    }

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Events\Websites\Created::class, [$this, 'create']);
        $events->listen(Events\Websites\Updated::class, [$this, 'updated']);
        $events->listen(Events\Websites\Deleted::class, [$this, 'delete']);
    }

    /**
     * @param Events\Websites\Created $event
     *
     * @throws GeneratorFailedException
     */
    public function create(Events\Websites\Created $event)
    {
        if (!config('tenancy.db.auto-create-tenant-database', true)) {
            return;
        }

        if ($this->mode !== Connection::DIVISION_MODE_SEPARATE_DATABASE) {
            return;
        }

        $config = $this->connection->generateConfigurationArray($event->website);

        $this->configureHost($config);

        $this->emitEvent(
            new Events\Database\Creating($config, $event->website)
        );

        $driver = Arr::get($config, 'driver', 'mysql');

        switch ($driver) {
            case 'mysql':
                $success = $this->createMysql($config);
                break;
            case 'pgsql':
                $success = $this->createPostgres($config);
                break;
            default:
                throw new GeneratorFailedException("Could not generate database for driver $driver");
        }

        if (!$success) {
            throw new GeneratorFailedException("Could not generate database {$config['database']}, one of the statements failed.");
        }

        $this->emitEvent(
            new Events\Database\Created($config, $event->website)
        );
    }

    /**
     * @param array $config
     *
     * @return bool
     */
    protected function createMysql(array $config = [])
    {
        $create = function ($connection) use ($config) {
            return $connection->statement("CREATE DATABASE `{$config['database']}`");
        };
        $grant = function ($connection) use ($config) {
            return $connection->statement("GRANT ALL ON `{$config['database']}`.* TO `{$config['username']}`@'{$config['host']}' IDENTIFIED BY '{$config['password']}'");
        };

        return $this->connection->system()->transaction(function (IlluminateConnection $connection) use ($create, $grant) {
            return $create($connection) && $grant($connection);
        });
    }

    /**
     * Mutates specified host for remote connections.
     *
     * @param $config
     */
    protected function configureHost(&$config)
    {
        $host = Arr::get($config, 'host');

        if (!in_array($host, ['localhost', '127.0.0.1', '192.168.0.1'])) {
            $config['host'] = '%';
        }
    }

    /**
     * @param array $config
     *
     * @return bool
     */
    protected function createPostgres(array $config = [])
    {
        $connection = $this->connection->system();

        $user = function () use ($connection, $config) {
            return $connection->statement("CREATE USER \"{$config['username']}\" WITH PASSWORD '{$config['password']}'");
        };
        $create = function () use ($connection, $config) {
            return $connection->statement("CREATE DATABASE \"{$config['database']}\" WITH OWNER=\"{$config['username']}\"");
        };
        $grant = function () use ($connection, $config) {
            return $connection->statement("GRANT ALL PRIVILEGES ON DATABASE \"{$config['database']}\" TO \"{$config['username']}\"");
        };

        return $user() && $create() && $grant();
    }

    /**
     * @param Events\Websites\Deleted $event
     *
     * @throws GeneratorFailedException
     */
    public function delete(Events\Websites\Deleted $event)
    {
        if (!config('tenancy.db.auto-delete-tenant-database', false)) {
            return;
        }

        if ($this->mode !== Connection::DIVISION_MODE_SEPARATE_DATABASE) {
            return;
        }

        $config = $this->connection->generateConfigurationArray($event->website);

        $this->emitEvent(
            new Events\Database\Deleting($config, $event->website)
        );

        $statement = "DROP DATABASE IF EXISTS `{$config['database']}`";

        if (Arr::get($config, 'driver') === 'pgsql') {
            $statement = "DROP DATABASE IF EXISTS \"{$config['database']}\"";
        }

        if (!$this->connection->system()->statement($statement)) {
            throw new GeneratorFailedException("Could not delete database {$config['database']}, the statement failed.");
        }

        $this->emitEvent(
            new Events\Database\Deleted($config, $event->website)
        );
    }

    /**
     * @param Events\Websites\Updated $event
     *
     * @throws GeneratorFailedException
     */
    public function updated(Events\Websites\Updated $event)
    {
        if (!config('tenancy.db.auto-rename-tenant-database', false)) {
            return;
        }

        if ($this->mode !== Connection::DIVISION_MODE_SEPARATE_DATABASE) {
            return;
        }

        $uuid = Arr::get($event->dirty, 'uuid');

        if (!$uuid) {
            return;
        }

        $config = $this->connection->generateConfigurationArray($event->website);

        $this->emitEvent(
            new Events\Database\Renaming($config, $event->website)
        );

        $statement = "RENAME TABLE `$uuid`.table TO `{$config['database']}`.table";

        if (Arr::get($config, 'driver') === 'pgsql') {
            $statement = "ALTER DATABASE \"$uuid\" RENAME TO \"{$config['database']}\"";
        }

        if (!$this->connection->system()->statement($statement)) {
            throw new GeneratorFailedException("Could not delete database {$config['database']}, the statement failed.");
        }

        $this->emitEvent(
            new Events\Database\Renamed($config, $event->website)
        );
    }
}
