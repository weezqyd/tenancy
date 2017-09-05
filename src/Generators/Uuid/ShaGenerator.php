<?php

namespace Elimuswift\Tenancy\Generators\Uuid;

use Elimuswift\Tenancy\Contracts\Website\UuidGenerator;
use Elimuswift\Tenancy\Models\Website;
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
