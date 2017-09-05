<?php

namespace Elimuswift\Tenancy\Validators;

use Elimuswift\Tenancy\Abstracts\Validator;

class HostnameValidator extends Validator
{
    protected $create = [
        'fqdn' => ['required', 'string'],
        'redirect_to' => ['string', 'url'],
        'force_https' => ['boolean'],
        'under_maintenance_since' => ['date'],
        'website_id' => ['integer', 'exists:websites,id'],
        'customer_id' => ['integer', 'exists:customers,id'],
    ];

    protected $update = [
        'id' => ['required', 'integer'],
        'fqdn' => ['required', 'string'],
        'redirect_to' => ['string', 'url'],
        'force_https' => ['boolean'],
        'under_maintenance_since' => ['date'],
        'website_id' => ['integer', 'exists:websites,id'],
        'customer_id' => ['integer', 'exists:customers,id'],
    ];
}
