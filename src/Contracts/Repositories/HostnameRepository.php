<?php

namespace Elimuswift\Tenancy\Contracts\Repositories;

use Elimuswift\Tenancy\Models\Hostname;
use Elimuswift\Tenancy\Models\Website;

interface HostnameRepository
{
    /**
     * @param string $hostname
     *
     * @return Hostname|null
     */
    public function findByHostname(string $hostname): ?Hostname;

    /**
     * @return Hostname|null
     */
    public function getDefault(): ?Hostname;

    /**
     * @param Hostname $hostname
     *
     * @return Hostname
     */
    public function create(Hostname &$hostname): Hostname;

    /**
     * @param Hostname $hostname
     *
     * @return Hostname
     */
    public function update(Hostname &$hostname): Hostname;

    /**
     * @param Hostname $hostname
     * @param bool     $hard
     *
     * @return Hostname
     */
    public function delete(Hostname &$hostname, $hard = false): Hostname;

    /**
     * @param Hostname $hostname
     * @param Website  $website
     *
     * @return Hostname
     */
    public function attach(Hostname &$hostname, Website &$website): Hostname;

    /**
     * @param Hostname $hostname
     *
     * @return Hostname
     */
    public function detach(Hostname &$hostname): Hostname;

    /**
     * Try to find a host.
     *
     * @param string $key
     *
     * @return Hostname|null
     **/
    public function find(string $key): ?Hostname;
}
