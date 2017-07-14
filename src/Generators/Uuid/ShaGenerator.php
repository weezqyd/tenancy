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

namespace Hyn\Tenancy\Generators\Uuid;

use Hyn\Tenancy\Contracts\Website\UuidGenerator;
use Hyn\Tenancy\Models\Website;
use Ramsey\Uuid\Uuid;

class ShaGenerator implements UuidGenerator
{
    /**
     * @param Website $website
     *
     * @return string
     */
    public function generate(Website $website): string
    {
        return Uuid::uuid4();
    }
}
