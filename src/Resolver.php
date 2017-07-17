<?php

/*
 * This file is part of the hyn/multi-tenant package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/hyn/multi-tenant
 *
 */

namespace Hyn\Tenancy;

use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Events\Hostnames\Identified;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Traits\DispatchesEvents;
use Illuminate\Http\Request;

class Resolver
{
    use DispatchesEvents;

    /**
     * Create Resolver Instance.
     *
     * @param Request            $request
     * @param HostnameRepository $hostnameRepository
     */
    public function __construct(Request $request, HostnameRepository $hostnameRepository)
    {
        $this->request = $request;
        $this->hostname = $hostnameRepository;
    }

    /**
     * @return Hostname|null
     */
    public function resolve(): ?Hostname
    {
        $hostname = env('TENANCY_CURRENT_HOSTNAME');

        if (!$hostname) {
            $hostname = $this->request->server('SERVER_NAME');
        }

        $hostname = $this->hostname->findByHostname($hostname);

        if (!$hostname) {
            $hostname = $this->hostname->getDefault();
        }

        if ($hostname) {
            $this->emitEvent(new Identified($hostname));
        }

        return $hostname;
    }
}
