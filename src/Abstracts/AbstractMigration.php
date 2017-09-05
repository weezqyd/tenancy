<?php

namespace Elimuswift\Tenancy\Abstracts;

use Elimuswift\Tenancy\Database\Connection;
use Illuminate\Database\Migrations\Migration;

abstract class AbstractMigration extends Migration
{
    protected $system = null;

    abstract public function up();

    abstract public function down();

    public function getConnection()
    {
        if ($this->system === true) {
            return $this->connectionResolver()->systemName();
        }

        if ($this->system === false) {
            return $this->connectionResolver()->tenantName();
        }

        return $this->connection;
    }

    /**
     * @return Connection
     */
    protected function connectionResolver()
    {
        return app(Connection::class);
    }
}
