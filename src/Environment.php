<?php

namespace Elimuswift\Tenancy;

use Elimuswift\Tenancy\Contracts\CurrentHostname;
use Elimuswift\Tenancy\Events\Hostnames\Switched;
use Elimuswift\Tenancy\Traits\DispatchesEvents;
use Elimuswift\Tenancy\Traits\DispatchesJobs;
use Illuminate\Contracts\Foundation\Application;

class Environment
{
    use DispatchesJobs, DispatchesEvents;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->identifyHostname();
    }

    /**
     * Auto identification of the tenant hostname to use.
     */
    public function identifyHostname()
    {
        $this->app->singleton(CurrentHostname::class, function ($app) {
            return $app->resolver->resolve($app->request);
        });
    }

    /**
     * @return Models\Customer|null
     */
    public function customer(): ?Models\Customer
    {
        $hostname = $this->hostname();

        return $hostname ? $hostname->customer : null;
    }

    /**
     * Get or set the current hostname.
     *
     * @param Models\Hostname|null $model
     *
     * @return Models\Hostname|null
     */
    public function hostname(Models\Hostname $model = null): ?Models\Hostname
    {
        if (null !== $model) {
            $this->emitEvent(new Switched($model));

            return $model;
        }

        return $this->app->make(CurrentHostname::class);
    }

    /**
     * @return Models\Website|bool
     */
    public function website(): ?Models\Website
    {
        $hostname = $this->hostname();

        return $hostname ? $hostname->website : null;
    }
}
