<?php

namespace Elimuswift\Tenancy\Abstracts;

use Elimuswift\Tenancy\Database\Connection;

abstract class SystemModel extends AbstractModel
{
    public function getConnectionName()
    {
        return app(Connection::class)->systemName();
    }
}
