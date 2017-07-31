<?php

namespace Elimuswift\Tenancy\Models;

use Carbon\Carbon;
use Elimuswift\Tenancy\Abstracts\SystemModel;
use Elimuswift\Tenancy\Contracts\CurrentHostname;

/**
 * @property int $id
 * @property string $fqdn
 * @property string $redirect_to
 * @property bool $force_https
 * @property Carbon $under_maintenance_since
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property int $website_id
 * @property Website $website
 * @property int $customer_id
 * @property Customer $customer
 */
class Hostname extends SystemModel implements CurrentHostname
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
