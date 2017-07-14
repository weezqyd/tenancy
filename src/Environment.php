<?php

/*
 * This file is part of the hyn/multi-tenant package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://laravel-tenancy.com
 * @see https://github.com/hyn/multi-tenant
 */

namespace Hyn\Tenancy;

use Hyn\Tenancy\Contracts\CurrentHostname;
use Hyn\Tenancy\Events\Hostnames\Switched;
use Hyn\Tenancy\Traits\DispatchesEvents;
use Hyn\Tenancy\Traits\DispatchesJobs;
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

        if (config('tenancy.hostname.auto-identification')) {
            $this->identifyHostname($app[Resolver::class]);
            // Resolve
            $this->app[Database\Connection::class]->set($this->app[CurrentHostname::class]);
        }
    }

    /**
     * Auto identification of the tenant hostname to use.
     */
    public function identifyHostname($resolver)
    {
        $this->app->singleton(CurrentHostname::class, function () use ($resolver) {
            return $resolver->resolve();
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
        if ($model !== null) {
            $this->app->singleton(CurrentHostname::class, function () use ($model) {
                return $model;
            });

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
