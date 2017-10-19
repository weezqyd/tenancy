<?php

namespace Elimuswift\Tenancy\Generators\Uuid;

use Elimuswift\Tenancy\Contracts\Website\UuidGenerator;
use Elimuswift\Tenancy\Models\Website;
use Elimuswift\Tenancy\Exceptions\GeneratorInvalidException;

class ShaGenerator implements UuidGenerator
{
    /**
     * @param Website $website
     *
     * @return string
     */
    public function generate(Website $website, int $lenght = 13): string
    {
        if (function_exists('random_bytes')) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new GeneratorInvalidException('no cryptographically secure random function available');
        }

        return substr(bin2hex($bytes), 0, $lenght);
    }
}
