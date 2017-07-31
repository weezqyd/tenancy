<?php

namespace Elimuswift\Tenancy\Tests\Database;

use Elimuswift\Tenancy\Database\Connection;
use Elimuswift\Tenancy\Tests\Test;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Connection as DatabaseConnection;

class ConnectionTest extends Test
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function without_identification_no_tenant_connection_is_active()
    {
        $this->setUpHostnames(true);

        $this->connection->get();
    }

    /**
     * @test
     * @depends without_identification_no_tenant_connection_is_active
     */
    public function hostname_identification_switches_connection()
    {
        $this->setUpHostnames(true);
        $this->activateTenant('local');

        $failsWithoutWebsite = false;

        try {
            $this->connection->get();
        } catch (\InvalidArgumentException $e) {
            $failsWithoutWebsite = true;
        }

        $this->assertTrue($failsWithoutWebsite, 'Tenant connection should not work, when the hostname has no website.');
        $this->assertTrue($this->connection->system() instanceof DatabaseConnection, 'System connection is not working.');
    }

    /**
     * @test
     * @depends hostname_identification_switches_connection
     */
    public function both_connections_work()
    {
        $this->setUpHostnames(true);
        $this->setUpWebsites(true, true);
        $this->activateTenant('local');

        $this->assertTrue($this->connection->get() instanceof DatabaseConnection, 'Tenant connection is not set up properly.');
        $this->assertTrue($this->connection->system() instanceof DatabaseConnection, 'System connection fails once tenant connection is set up.');
    }

    /**
     * @test
     * @depends both_connections_work
     */
    public function can_migrate_the_tenant()
    {
        config(['tenancy.db.tenant-migrations-path' => __DIR__ . '/../../migrations']);

        $this->assertNotNull(config('tenancy.db.tenant-migrations-path'));

        $this->setUpHostnames(true);
        $this->setUpWebsites(true, true);
        $this->activateTenant('local');

        $this->assertTrue($this->connection->get()->getSchemaBuilder()->hasTable('samples'));
    }

    protected function duringSetUp(Application $app)
    {
        $this->connection = $app->make(Connection::class);
    }
}
