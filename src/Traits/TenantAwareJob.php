<?php

namespace Elimuswift\Tenancy\Traits;

use Elimuswift\Tenancy\Environment;
use Elimuswift\Tenancy\Models\Hostname;
use Illuminate\Queue\SerializesModels;

trait TenantAwareJob
{
    /**
     * @var int the hostname ID of the previously active tenant
     */
    protected $hostname_id;

    use SerializesModels {
        __sleep as serializedSleep;
        __wakeup as serializedWakeup;
    }

    public function __sleep()
    {
        /** @var Environment $environment */
        $environment = app(Environment::class);

        if ($hostname = $environment->hostname()) {
            $this->hostname_id = $hostname->id;
        }

        $attributes = $this->serializedSleep();

        return $attributes;
    }

    public function __wakeup()
    {
        if (isset($this->hostname_id)) {
            /** @var Environment $environment */
            $environment = app(Environment::class);

            $environment->hostname(Hostname::find($this->hostname_id));
        }

        $this->serializedWakeup();
    }
}
