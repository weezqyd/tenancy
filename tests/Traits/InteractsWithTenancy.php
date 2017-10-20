<?php

namespace Elimuswift\Tenancy\Tests\Traits;

use Illuminate\Support\Facades\DB;
use Elimuswift\Tenancy\Contracts\Repositories\HostnameRepository;
use Elimuswift\Tenancy\Contracts\Repositories\WebsiteRepository;
use Elimuswift\Tenancy\Events\Hostnames\Identified;
use Elimuswift\Tenancy\Models\Hostname;
use Elimuswift\Tenancy\Models\Website;
use Elimuswift\Tenancy\Traits\DispatchesEvents;

trait InteractsWithTenancy
{
    use DispatchesEvents;
    /**
     * @var Hostname
     */
    protected $hostname;

    /**
     * @var Hostname
     */
    protected $tenant;

    /**
     * @var Website
     */
    protected $website;

    /**
     * @var HostnameRepository
     */
    protected $hostnames;
    /**
     * @var WebsiteRepository
     */
    protected $websites;

    protected function setUpTenancy()
    {
        $this->websites = app(WebsiteRepository::class);
        $this->hostnames = app(HostnameRepository::class);
    }

    protected function loadHostnames()
    {
        $this->hostname = Hostname::where('fqdn', 'local.testing')->firstOrFail();
        $this->tenant = Hostname::where('fqdn', 'tenant.testing')->firstOrFail();
    }

    /**
     * @param bool $save
     */
    protected function setUpHostnames(bool $save = false)
    {
        Hostname::unguard();

        $hostname = new Hostname([
            'fqdn' => 'local.testing',
        ]);

        $this->hostname = $hostname;

        $tenant = new Hostname([
            'fqdn' => 'tenant.testing',
        ]);

        $this->tenant = $tenant;

        Hostname::reguard();

        if ($save) {
            $this->hostnames->create($this->tenant);
            $this->hostnames->create($this->hostname);
        }
    }

    /**
     * @param string $tenant
     */
    protected function activateTenant(string $tenant)
    {
        $hostname = $tenant == 'tenant' ? $this->tenant : $this->hostname;

        $this->emitEvent(
            new Identified($hostname)
        );
    }

    protected function loadWebsites()
    {
        $this->website = Website::firstOrFail();
    }

    /**
     * @param bool $save
     * @param bool $connect
     */
    protected function setUpWebsites(bool $save = false, bool $connect = false)
    {
        $this->website = new Website();

        if ($save) {
            $this->websites->create($this->website);
        }

        if ($connect) {
            $this->website->hostnames()->save($this->hostname);
            $this->activateTenant('tenant');
        }
    }

    protected function cleanupTenancy()
    {
        foreach (['website', 'hostname', 'tenant'] as $property) {
            if ($this->{$property} && $this->{$property}->exists) {
                if ($property === 'website') {
                    DB::statement("DROP USER IF EXISTS '{$this->website->uuid}'@'localhost'");
                    DB::statement("DROP DATABASE IF EXISTS `{$this->website->uuid}`");
                }
                $this->{$property}->delete();
            }
        }
    }
}
