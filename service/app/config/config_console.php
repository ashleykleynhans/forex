<?php

/**
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */

return new \Phalcon\Config([
    'database' => [
        'adapter'    => 'Mysql',
        'host'       => '127.0.0.1',
        'username'   => 'forex',
        'password'   => get_cfg_var('DB_PASSWORD'),
        'dbname'     => 'forex',
        'charset'    => 'utf8',
        'debug'      => false,
    ],

    'rates_api' => [
        'url'        => 'http://www.apilayer.net/api/live',
        'access_key' => get_cfg_var('API_LAYER_KEY'),
        'currencies' => 'ZAR,GBP,EUR,KES',
        'source'     => 'USD',
    ]
]);
