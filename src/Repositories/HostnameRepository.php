<?php

namespace Elimuswift\Tenancy\Repositories;

use Elimuswift\Tenancy\Models\Hostname;
use Elimuswift\Tenancy\Models\Website;
use Illuminate\Contracts\Cache\Factory;
use Elimuswift\Tenancy\Traits\DispatchesEvents;
use Elimuswift\Tenancy\Events\Hostnames as Events;
use Illuminate\Database\Eloquent\Collection;
use Elimuswift\Tenancy\Validators\HostnameValidator;
use Elimuswift\Tenancy\Contracts\Repositories\HostnameRepository as Contract;

class HostnameRepository implements Contract
{
    use DispatchesEvents;
    /**
     * @var Hostname
     */
    protected $hostname;
    /**
     * @var HostnameValidator
     */
    protected $validator;
    /**
     * @var Factory
     */
    protected $cache;

    /**
     * HostnameRepository constructor.
     *
     * @param Hostname          $hostname
     * @param HostnameValidator $validator
     * @param Factory           $cache
     */
    public function __construct(Hostname $hostname, HostnameValidator $validator, Factory $cache)
    {
        $this->hostname = $hostname;
        $this->validator = $validator;
        $this->cache = $cache;
    }

    /**
     * @param string $hostname
     *
     * @return Hostname|null
     */
    public function findByHostname(string $hostname): ?Hostname
    {
        return $this->cache->remember("tenancy.hostname.$hostname", config('tenancy.hostname.cache'), function () use ($hostname) {
            return $this->hostname->newQuery()->where('fqdn', $hostname)->first();
        });
    }

    /**
     * @return Hostname|null
     */
    public function getDefault(): ?Hostname
    {
        if (config('tenancy.hostname.default')) {
            return $this->hostname->newQuery()->where('fqdn', config('tenancy.hostname.default'))->first();
        }

        return null;
    }

    /**
     * @param Hostname $hostname
     *
     * @return Hostname
     */
    public function create(Hostname &$hostname): Hostname
    {
        if ($hostname->exists) {
            return $this->update($hostname);
        }

        $this->emitEvent(
            new Events\Creating($hostname)
        );

        $this->validator->save($hostname);

        $hostname->save();

        $this->emitEvent(
            new Events\Created($hostname)
        );

        return $hostname;
    }

    /**
     * @param Hostname $hostname
     *
     * @return Hostname
     */
    public function update(Hostname &$hostname): Hostname
    {
        if (!$hostname->exists) {
            return $this->create($hostname);
        }

        $this->emitEvent(
            new Events\Updating($hostname)
        );

        $this->validator->save($hostname);

        $dirty = $hostname->getDirty();

        $hostname->save();

        $this->cache->forget("tenancy.hostname.{$hostname->fqdn}");

        $this->emitEvent(
            new Events\Updated($hostname, $dirty)
        );

        return $hostname;
    }

    /**
     * @param Hostname $hostname
     * @param bool     $hard
     *
     * @return Hostname
     */
    public function delete(Hostname &$hostname, $hard = false): Hostname
    {
        $this->emitEvent(
            new Events\Deleting($hostname)
        );

        $this->validator->delete($hostname);

        if ($hard) {
            $hostname->forceDelete();
        } else {
            $hostname->delete();
        }

        $this->cache->forget("tenancy.hostname.{$hostname->fqdn}");

        $this->emitEvent(
            new Events\Deleted($hostname)
        );

        return $hostname;
    }

    /**
     * @param Hostname $hostname
     * @param Website  $website
     *
     * @return Hostname
     */
    public function attach(Hostname &$hostname, Website &$website): Hostname
    {
        $website->hostnames()->save($hostname);

        $this->cache->forget("tenancy.hostname.{$hostname->fqdn}");

        $this->emitEvent(
            new Events\Attached($hostname, $website)
        );

        return $hostname;
    }

    /**
     * @param Hostname $hostname
     *
     * @return Hostname
     */
    public function detach(Hostname &$hostname): Hostname
    {
        $hostname->website_id = null;

        $this->update($hostname);

        $this->emitEvent(
            new Events\Detached($hostname)
        );

        return $hostname;
    }

    /**
     * Try to find a host.
     *
     * @param string $key
     *
     * @return Hostname
     **/
    public function find(string $key): ?Hostname
    {
        return $this->cache->remember("tenancy.hostname.$key", config('tenancy.hostname.cache'), function () use ($key) {
            return $this->hostname->newQuery()->where('fqdn', $key)->orWhere('id', $key)->first();
        });
    }

    /**
     * Get all Hostnames.
     *
     * @return Illuminate\Database\Collection
     **/
    public function getAll(): Collection
    {
        return $this->hostname->all();
    }
}
