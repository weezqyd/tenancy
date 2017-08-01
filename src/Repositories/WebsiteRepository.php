<?php

namespace Elimuswift\Tenancy\Repositories;

use Elimuswift\Tenancy\Models\Website;
use Illuminate\Contracts\Cache\Factory;
use Elimuswift\Tenancy\Traits\DispatchesEvents;
use Elimuswift\Tenancy\Events\Websites as Events;
use Elimuswift\Tenancy\Validators\WebsiteValidator;
use Illuminate\Database\Eloquent\Collection;
use Elimuswift\Tenancy\Contracts\Repositories\WebsiteRepository as Contract;

class WebsiteRepository implements Contract
{
    use DispatchesEvents;
    /**
     * @var Website
     */
    protected $website;
    /**
     * @var WebsiteValidator
     */
    protected $validator;
    /**
     * @var Factory
     */
    protected $cache;

    /**
     * WebsiteRepository constructor.
     *
     * @param Website          $website
     * @param WebsiteValidator $validator
     * @param Factory          $cache
     */
    public function __construct(Website $website, WebsiteValidator $validator, Factory $cache)
    {
        $this->website = $website;
        $this->validator = $validator;
        $this->cache = $cache;
    }

    /**
     * @param string $uuid
     *
     * @return Website|null
     */
    public function findByUuid(string $uuid): ?Website
    {
        return $this->cache->remember("tenancy.website.$uuid", config('tenancy.website.cache'), function () use ($uuid) {
            return $this->website->newQuery()->where('uuid', $uuid)->first();
        });
    }

    /**
     * @param Website $website
     *
     * @return Website
     */
    public function create(Website &$website): Website
    {
        if ($website->exists) {
            return $this->update($website);
        }

        $this->emitEvent(
            new Events\Creating($website)
        );

        $this->validator->save($website);

        $website->save();

        $this->cache->flush("tenancy.website.{$website->uuid}");

        $this->emitEvent(
            new Events\Created($website)
        );

        return $website;
    }

    /**
     * @param Website $website
     *
     * @return Website
     */
    public function update(Website &$website): Website
    {
        if (!$website->exists) {
            return $this->create($website);
        }

        $this->emitEvent(
            new Events\Updating($website)
        );

        $this->validator->save($website);

        $dirty = $website->getDirty();

        $website->save();

        $this->cache->flush("tenancy.website.{$website->uuid}");

        $this->emitEvent(
            new Events\Updated($website, $dirty)
        );

        return $website;
    }

    /**
     * @param Website $website
     * @param bool    $hard
     *
     * @return Website
     */
    public function delete(Website &$website, $hard = false): Website
    {
        $this->emitEvent(
            new Events\Deleting($website)
        );

        $this->validator->delete($website);

        $hard ? $website->forceDelete() : $website->delete();

        $this->cache->flush("tenancy.website.{$website->uuid}");

        $this->emitEvent(
            new Events\Deleted($website)
        );

        return $website;
    }

    /**
     * Find tenant by uuid or id.
     *
     * @param string $key uuid or id
     *
     * @return Website|null
     **/
    public function findByUuidOrId(string $key): ?Website
    {
        $byUuid = $this->findByUuid($key);

        return $byUuid ?? $this->cache->remember("tenancy.website.$key", config('tenancy.website.cache'), function () use ($key) {
            return $this->website->newQuery()->find($key);
        });
    }

    /**
     * Get all Hostnames.
     *
     * @return Illuminate\Database\Collection
     **/
    public function getAll(): Collection
    {
        return $this->website->newQuery()->get();
    }
}
