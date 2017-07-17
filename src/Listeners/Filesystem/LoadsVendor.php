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

namespace Hyn\Tenancy\Listeners\Filesystem;

use Hyn\Tenancy\Abstracts\AbstractTenantDirectoryListener;
use Hyn\Tenancy\Events\Hostnames\Identified;
use Hyn\Tenancy\Exceptions\FilesystemException;

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
