<?php

namespace Elimuswift\Tenancy\Contracts\Generator;

use Elimuswift\Tenancy\Models\Website;

interface GeneratesConfiguration
{
    /**
     * @param Website $website
     *
     * @return string
     */
    public function generate(Website $website): string;
}
