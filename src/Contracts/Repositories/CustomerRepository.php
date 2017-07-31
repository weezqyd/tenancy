<?php

namespace Elimuswift\Tenancy\Contracts\Repositories;

use Elimuswift\Tenancy\Models\Customer;

interface CustomerRepository
{
    /**
     * @param string $email
     *
     * @return Customer|null
     */
    public function findByEmail(string $email): ?Customer;

    /**
     * @param Customer $customer
     *
     * @return Customer
     */
    public function create(Customer &$customer): Customer;

    /**
     * @param Customer $customer
     *
     * @return Customer
     */
    public function update(Customer &$customer): Customer;

    /**
     * @param Customer $customer
     * @param bool     $hard
     *
     * @return Customer
     */
    public function delete(Customer &$customer, $hard = false): Customer;
}
