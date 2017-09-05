<?php

namespace Elimuswift\Tenancy\Events\Websites;

use Elimuswift\Tenancy\Abstracts\WebsiteEvent;
use Elimuswift\Tenancy\Models\Website;

class Updated extends WebsiteEvent
{
    /**
     * @var array
     */
    public $dirty;

    public function __construct(Website &$website, array $dirty = [])
    {
        parent::__construct($website);

        $this->dirty = $dirty;
    }
}
