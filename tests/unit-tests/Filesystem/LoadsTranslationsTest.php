<?php

namespace Elimuswift\Tenancy\Tests\Filesystem;

use Elimuswift\Tenancy\Tests\Test;
use Elimuswift\Tenancy\Website\Directory;
use Illuminate\Contracts\Foundation\Application;

class LoadsTranslationsTest extends Test
{
    /**
     * @var Directory
     */
    protected $directory;

    protected function duringSetUp(Application $app)
    {
        $this->setUpHostnames(true);
        $this->setUpWebsites(true, true);

        $this->directory = $app->make(Directory::class);
        $this->directory->setWebsite($this->website);
    }

    /**
     * @test
     */
    public function reads_additional_translations()
    {
        // Directory should now exists, let's write the config folder.
        $this->assertTrue($this->directory->makeDirectory('lang'));

        // Write a testing config.
        $this->assertTrue($this->directory->put(
            'lang' . DIRECTORY_SEPARATOR . 'ch' . DIRECTORY_SEPARATOR . 'test.php',
            <<<EOM
<?php

return [
    'foo' => 'bar'
];
EOM
));

        $this->assertTrue($this->directory->exists('lang/ch/test.php'));

        $this->activateTenant('local');

        if (!$this->isAppVersion('5.3')) {
            $this->assertEquals('bar', trans('test.foo', [], 'ch'));
        } else {
            $this->assertEquals('bar', trans('test.foo', [], 'messages', 'ch'));
        }
    }
}
