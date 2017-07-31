<?php

namespace Elimuswift\Tenancy\Abstracts;

use Elimuswift\Tenancy\Models\Website;

abstract class WebserverEvent extends AbstractEvent
{
    /**
     * @var Website
     */
    public $website;

    public function __construct(Website $website, string $service)
    {
        $this->website = $website;
    }
}
