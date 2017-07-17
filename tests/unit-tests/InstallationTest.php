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

namespace Hyn\Tenancy\Tests;

use Hyn\Tenancy\Contracts\CurrentHostname;
use Hyn\Tenancy\Contracts\Website\UuidGenerator;
use Hyn\Tenancy\Generators\Uuid\ShaGenerator;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Providers\TenancyProvider;
use Hyn\Tenancy\Providers\Tenants as Providers;
use Hyn\Tenancy\Providers\WebserverProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;

class InstallationTest extends Test
{
    /**
     * @test
     */
    public function service_providers_registered()
    {
        foreach ([
                     TenancyProvider::class,
                     WebserverProvider::class,
                     Providers\BusProvider::class,
                     Providers\ConfigurationProvider::class,
                     Providers\ConnectionProvider::class,
                     Providers\EventProvider::class,
                     Providers\FilesystemProvider::class,
                     Providers\PasswordProvider::class,
                     Providers\UuidProvider::class,
                 ] as $provider) {
            $this->assertTrue(
                Arr::get($this->app->getLoadedProviders(), $provider, false),
                "$provider is not registered"
            );
        }
    }

    /**
     * @test
     */
    public function configurations_are_loaded()
    {
        $this->assertFalse(config('tenancy.website.disable-random-id'));
    }

    /**
     * @test
     */
    public function publishes_vendor_files()
    {
        $code = $this->artisan('vendor:publish', [
            '--tag' => 'tenancy',
            '--provider' => Providers\ConfigurationProvider::class,
            '-n' => 1,
        ]);

        $this->assertEquals(0, $code, 'Publishing vendor files failed');

        $this->assertFileExists(config_path('tenancy.php'));
    }

    /**
     * @test
     */
    public function install_command_works()
    {
        $code = $this->artisan('migrate:reset', [
            '-n' => 1,
        ]);

        $this->assertEquals(0, $code, 'Resetting migrations didn\'t work out');

        $code = $this->artisan('tenancy:install', [
            '-n' => 1,
        ]);

        $this->assertEquals(0, $code, 'Installation didn\'t work out');
    }

    /**
     * @test
     * @depends install_command_works
     */
    public function migration_succeeded()
    {
        $works = true;

        try {
            Hostname::first();
        } catch (QueryException $e) {
            $works = false;
        }

        $this->assertTrue($works, 'Database not migrated');
    }

    /**
     * @test
     * @depends migration_succeeded
     */
    public function saves_default_hostname()
    {
        $this->setUpHostnames();

        $this->hostnames->create($this->hostname);

        $this->assertTrue($this->hostname->exists);
    }

    /**
     * @test
     * @depends saves_default_hostname
     */
    public function hostname_identification_returns_default()
    {
        $this->setUpHostnames(true);

        $this->assertEquals(
            $this->hostname->fqdn,
            $this->app->make(CurrentHostname::class)->fqdn
        );
    }

    /**
     * @test
     * @depends saves_default_hostname
     */
    public function verify_request()
    {
        $this->setUpHostnames(true);

        $response = $this->get('http://localhost/default');

        $this->assertJsonFragment(['fqdn' => $this->hostname->fqdn], $response);
    }

    /**
     * @test
     * @depends verify_request
     */
    public function save_tenant_hostname()
    {
        $this->setUpHostnames();

        $this->hostnames->create($this->tenant);

        $this->assertTrue($this->tenant->exists);
    }

    /**
     * @test
     * @depends save_tenant_hostname
     */
    public function verify_tenant_request()
    {
        $this->setUpHostnames(true);

        $response = $this->get('http://tenant.testing/default', ['host' => $this->tenant->fqdn]);

        $this->assertJsonFragment(['fqdn' => $this->tenant->fqdn], $response);
    }

    /**
     * @test
     */
    public function verify_uuid_generator()
    {
        $generator = $this->app->make(UuidGenerator::class);

        $this->assertInstanceOf(ShaGenerator::class, $generator);
    }

    /**
     * @param Application $app
     */
    protected function duringSetUp(Application $app)
    {
        $router = $app->make(Router::class);

        $router->get('default', function () {
            return app(CurrentHostname::class)->toJson();
        });
    }
}
