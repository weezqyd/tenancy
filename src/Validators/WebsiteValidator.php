<?php

namespace Elimuswift\Tenancy\Validators;

use Elimuswift\Tenancy\Abstracts\Validator;

class WebsiteValidator extends Validator
{
    protected $create = [
        'uuid' => ['required', 'string'],
        'customer_id' => ['integer', 'exists:customers,id'],
    ];
    protected $update = [
        'uuid' => ['required', 'string'],
        'customer_id' => ['integer', 'exists:customers,id'],
    ];
}
