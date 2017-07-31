<?php

namespace Elimuswift\Tenancy\Abstracts;

use Elimuswift\Tenancy\Models\Customer;

abstract class CustomerEvent extends AbstractEvent
{
    /**
     * @var Customer
     */
    public $customer;

    public function __construct(Customer &$customer)
    {
        $this->customer = &$customer;
    }
}
