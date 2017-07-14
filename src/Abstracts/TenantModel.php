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

namespace Hyn\Tenancy\Abstracts;

use Hyn\Tenancy\Database\Connection;

abstract class TenantModel extends AbstractModel
{
    public function getConnectionName()
    {
        return app(Connection::class)->tenantName();
    }
}
