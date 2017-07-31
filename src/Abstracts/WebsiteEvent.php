<?php

namespace Elimuswift\Tenancy\Abstracts;

use Elimuswift\Tenancy\Models\Website;

abstract class WebsiteEvent extends AbstractEvent
{
    /**
     * @var Website
     */
    public $website;

    public function __construct(Website &$website)
    {
        $this->website = &$website;
    }
}
