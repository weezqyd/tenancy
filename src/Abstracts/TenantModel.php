<?php

namespace Elimuswift\Tenancy\Abstracts;

use Elimuswift\Tenancy\Database\Connection;

abstract class TenantModel extends AbstractModel
{
    public function getConnectionName()
    {
        return app(Connection::class)->tenantName();
    }
}
