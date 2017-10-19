<?php

namespace Elimuswift\Tenancy\Commands;

use Elimuswift\Tenancy\Models\Website;
use Illuminate\Console\Command;

class TenantsCommand extends Command
{
    protected $signature = 'tenancy:list';
    protected $description = 'List all tenants available in the system';

    public function handle()
    {
        $headers = ['Id', 'Uuid', 'Customer'];
        $data = Website::all()->map(function ($website) {
            return [
                'Id' => $website->id,
                'Uuid' => $website->uuid,
                'Customer' => $website->customer->name,
            ];
        })->all();
        $this->table($headers, $data);
    }
}
