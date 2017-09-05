<?php

namespace Elimuswift\Tenancy\Listeners\Filesystem;

use Elimuswift\Tenancy\Abstracts\AbstractTenantDirectoryListener;
use Elimuswift\Tenancy\Events\Hostnames\Identified;
use Elimuswift\Tenancy\Exceptions\FilesystemException;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

class LoadsTranslations extends AbstractTenantDirectoryListener
{
    /**
     * @var string
     */
    protected $configBaseKey = 'tenancy.folders.trans';

    /**
     * @var string
     */
    protected $path = 'lang';

    /**
     * @param Identified $event
     *
     * @throws FilesystemException
     */
    public function load(Identified $event)
    {
        if ($this->directory->isLocal()) {
            $this->readLanguageFiles($this->directory->path($this->path, true));
        } else {
            throw new FilesystemException("$this->path is not available locally, cannot include");
        }
    }

    /**
     * @param string $path
     */
    protected function readLanguageFiles(string $path)
    {
        if ($this->config->get('tenancy.folders.trans.override-global')) {
            app()->singleton('translation.loader', function ($app) use ($path) {
                return new FileLoader($app['files'], $path);
            });
            app()->singleton('translator', function ($app) {
                $translator = new Translator($app['translation.loader'], $app['config']['app.locale']);
                $translator->setFallback($app['config']['app.fallback_locale']);

                return $translator;
            });
        } elseif ($namespace = $this->config->get('tenancy.folders.trans.namespace')) {
            app('translator')->addNamespace($namespace, $path);
        }
    }
}
