<?php

namespace Elimuswift\Tenancy\Generators\Webserver\Vhost;

use Elimuswift\Tenancy\Contracts\Webserver\ReloadsServices;
use Elimuswift\Tenancy\Contracts\Webserver\VhostGenerator;
use Elimuswift\Tenancy\Models\Website;

class ApacheGenerator implements VhostGenerator, ReloadsServices
{
    /**
     * @param Website $website
     *
     * @return string
     */
    public function generate(Website $website): string
    {
        return view(
            'tenancy.generator::webserver.apache.vhost',
            [
                'website' => $website,
                'config' => config('webserver.apache2', []),
                'media' => true,
            ]
        );
    }

    /**
     * @param Website $website
     *
     * @return string
     */
    public function targetPath(Website $website): string
    {
        return "{$website->uuid}.conf";
    }

    /**
     * @return bool
     */
    public function reload(): bool
    {
        $success = null;

        if ($this->testConfiguration()) {
            exec(config('webserver.apache2.paths.actions.reload'), $_, $success);
        }

        return $success;
    }

    /**
     * @return bool
     */
    public function testConfiguration(): bool
    {
        exec(config('webserver.apache2.paths.actions.test-config'), $_, $success);

        return $success;
    }
}
