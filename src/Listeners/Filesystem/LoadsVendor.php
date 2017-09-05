<?php

namespace Elimuswift\Tenancy\Listeners\Filesystem;

use Elimuswift\Tenancy\Abstracts\AbstractTenantDirectoryListener;
use Elimuswift\Tenancy\Events\Hostnames\Identified;
use Elimuswift\Tenancy\Exceptions\FilesystemException;

class LoadsVendor extends AbstractTenantDirectoryListener
{
    protected $configBaseKey = 'tenancy.folders.vendor';

    /**
     * @var string
     */
    protected $path = 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    /**
     * @param Identified $event
     *
     * @throws FilesystemException
     */
    public function load(Identified $event)
    {
        if ($this->directory->isLocal()) {
            $this->directory->requireOnce($this->path);
        } else {
            throw new FilesystemException("$this->path is not available locally, cannot include");
        }
    }
}
