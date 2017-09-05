<?php

namespace Elimuswift\Tenancy\Tests\Webserver\Vhost;

use Elimuswift\Tenancy\Generators\Webserver\Vhost\ApacheGenerator;
use Elimuswift\Tenancy\Listeners\Servant;
use Elimuswift\Tenancy\Tests\Test;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;

class ApacheGeneratorTest extends Test
{
    /**
     * @var ApacheGenerator
     */
    protected $generator;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    protected function duringSetUp(Application $app)
    {
        // Marks all tests in this class as skipped.
        if ($this->buildWebserver != 'apache') {
            $this->markTestSkipped('Testing a different driver: ' . $this->buildWebserver);
        }

        $this->setUpWebsites();
        $this->setUpHostnames();
        $app['config']->set('webserver.apache2.enabled', true);

        $this->generator = $app->make(ApacheGenerator::class);
        $this->filesystem = app(Servant::class)->serviceFilesystem('apache2', config('webserver.apache2', []));
    }

    /**
     * @test
     */
    public function generates_vhost_configuration()
    {
        $this->websites->create($this->website);

        $path = $this->generator->targetPath($this->website);

        $this->assertTrue($this->filesystem->exists($path));
    }
}
