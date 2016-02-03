<?php

/**
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */

$config = new \Phalcon\Config([
    'database' => [
        'adapter'    => 'Mysql',
        'host'       => '127.0.0.1',
        'username'   => 'forex',
        'password'   => getenv('DB_PASSWORD'),
        'dbname'     => 'forex',
        'charset'    => 'utf8',
        'debug'      => false,
    ],
]);
