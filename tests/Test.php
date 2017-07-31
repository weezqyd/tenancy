<?php

namespace Elimuswift\Tenancy\Tests;

use Elimuswift\Tenancy\Providers\TenancyProvider;
use Elimuswift\Tenancy\Providers\WebserverProvider;
use Elimuswift\Tenancy\Tests\Traits\InteractsWithBuilds;
use Elimuswift\Tenancy\Tests\Traits\InteractsWithLaravelVersions;
use Elimuswift\Tenancy\Tests\Traits\InteractsWithTenancy;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;

/**
 * Class Test.
 */
class Test extends TestCase
{
    use InteractsWithTenancy,
        InteractsWithLaravelVersions,
        InteractsWithBuilds;

    /**
     * Service providers to load during this test.
     *
     * @var array
     */
    protected $loadProviders = [
        TenancyProvider::class,
        WebserverProvider::class,
    ];

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $appPaths = [];
        if (getenv('CI_PROJECT_DIR')) {
            $appPaths[] = realpath(getenv('CI_PROJECT_DIR') . '/vendor/laravel/laravel');
        }
        $appPaths[] = realpath(__DIR__ . '/..');
        $appPaths[] = realpath(__DIR__ . '/../vendor/laravel/laravel');

        foreach ($appPaths as $path) {
            $path = "$path/bootstrap/app.php";
            if (file_exists($path)) {
                /** @var Application $app */
                $app = require $path;
                break;
            }
        }

        if (!$app) {
            throw new \RuntimeException('No bootstrap file found, make sure laravel/laravel is installed');
        }

        $app->make(Kernel::class)->bootstrap();

        foreach ($this->loadProviders as $provider) {
            if (!$app->register($provider)) {
                throw new \RuntimeException("Failed registering $provider");
            }
        }

        $this->setSchemaLength($app);

        $this->identifyBuild();
        $this->setUpTenancy();
        $this->duringSetUp($app);

        return $app;
    }

    protected function setSchemaLength($app)
    {
        if (!$this->isAppVersion('5.3', $app)) {
            \Schema::defaultStringLength(191);
        }
    }

    /**
     * Allows implementation in a test.
     *
     * @param Application $app
     */
    protected function duringSetUp(Application $app)
    {
        // ..
    }

    protected function tearDown()
    {
        $this->cleanupTenancy();
        parent::tearDown();
    }
}
