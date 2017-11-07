<?php

namespace Elimuswift\Tenancy\Commands;

use Elimuswift\Tenancy\Models\Website;
use Illuminate\Console\Command;
use Elimuswift\Tenancy\Events\Websites\Updated;
use Elimuswift\Tenancy\Generators\Supervisor\SupervisorConfiguration;

class RegenerateWokersCommand extends Command
{
    protected $signature = 'tenancy:regenerate-workers';
    protected $description = 'Generate supervisor config files';

    public function handle()
    {
        $headers = ['Id', 'Uuid', 'Customer'];
        $data = Website::all()->map(function ($website) {
            $event = new Updated($website, $website->toarray());
            $generator = new SupervisorConfiguration();
            $generator->update($event);
            $this->info("Configuration for {$website->uuid} has been generated \n");
        });
    }
}
