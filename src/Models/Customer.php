<?php

namespace Elimuswift\Tenancy\Models;

use Carbon\Carbon;
use Elimuswift\Tenancy\Abstracts\SystemModel;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $primary_phone,
 * @property string $website,
 * @property string $city,
 * @property string $address,
 * @property float $tax,
 * @property int $currency_id,
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property Website[] $websites
 * @property Hostname[] $hostnames
 */
class Customer extends SystemModel
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function websites()
    {
        return $this->hasMany(Website::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hostnames()
    {
        return $this->hasMany(Hostname::class);
    }
}
