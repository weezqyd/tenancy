<?php

namespace Elimuswift\Tenancy;

use Elimuswift\Tenancy\Contracts\Repositories\HostnameRepository;
use Elimuswift\Tenancy\Events\Hostnames\Identified;
use Elimuswift\Tenancy\Models\Hostname;
use Elimuswift\Tenancy\Traits\DispatchesEvents;
use Illuminate\Http\Request;

class Resolver
{
    use DispatchesEvents;

    /**
     * Http request.
     *
     * @var Request
     **/
    protected $request;

    /**
     * Host names repository.
     *
     * @var HostnameRepository
     **/
    protected $hostname;

    /**
     * The currently resolved host.
     *
     * @var Hostname
     **/
    protected $currentHost;

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
            $this->currentHost = $hostname;
            $this->emitEvent(new Identified($hostname));
        }

        return $hostname;
    }

    /**
     *Get the resolved hostname.
     *
     * @return Hostname
     **/
    public function currentHost()
    {
        return $this->currentHost ?? new Hostname();
    }
}
