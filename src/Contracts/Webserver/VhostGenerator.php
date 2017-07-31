<?php

namespace Elimuswift\Tenancy\Contracts\Webserver;

use Elimuswift\Tenancy\Contracts\Generator\GeneratesConfiguration;
use Elimuswift\Tenancy\Contracts\Generator\SavesToPath;

interface VhostGenerator extends GeneratesConfiguration, SavesToPath
{
}
