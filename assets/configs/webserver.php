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

return [
    /*
     * Apache2 is one of the most widely adopted webserver packages available.
     *
     * @see http://httpd.apache.org/docs/
     * @see https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu
     */
    'apache2' => [
        /*
         * Whether the integration with Apache2 is currently active.
         *
         * @see
         */
        'enabled' => true,

        /*
         * Define the ports of your Apache service.
         */
        'ports' => [
            /*
             * HTTP, non-SSL port.
             *
             * @default 80
             */
            'http' => 80,
            /*
             * HTTPS, SSL port.
             *
             * @default 443
             */
            'https' => 443,
        ],

        /*
         * The generator taking care of hooking into the Apache services and files.
         */
        'generator' => \Hyn\Tenancy\Generators\Webserver\Vhost\ApacheGenerator::class,

        /*
         * Specify the disk you configured in the filesystems.php file where to store
         * the tenant vhost configuration files.
         *
         * @see
         * @info If not set, will revert to the default filesystem.
         */
        'disk' => null,

        'paths' => [
            /*
             * Location where vhost configuration files can be found.
             *
             * @see https://hyn.readme.io/v3.0/docs/webserverphp#section-apachepathsvhost-files
             */
            'vhost-files' => [
                '/etc/apache2/sites-enabled/',
            ],

            'actions' => [
                'exists' => '/etc/init.d/apache2',
                'test-config' => 'apache2ctl -t',
                'reload' => 'apache2ctl graceful',
            ],
        ],
    ],
    /**
     * Configuration options for supervisor queue worker
     */
    'supervisor' => [
        'prefix' => 'elimuswift',
        'config-path' => 'supervisor',
        'user' => 'www-data',
        'numprocs' => 4,
    ],
];
