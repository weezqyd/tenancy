<?php

namespace Elimuswift\Tenancy\Contracts\Generator;

use Elimuswift\Tenancy\Models\Website;

interface SavesToPath
{
    /**
     * @param Website $website
     *
     * @return string
     */
    public function targetPath(Website $website): string;
}
