<?php
/**
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();

$loader->registerDirs(
    [
        realpath(__DIR__ . '/../libs/'),
        realpath(__DIR__ . '/../models/'),
        realpath(__DIR__ . '/../constants/'),
        realpath(__DIR__ . '/../controllers/'),
    ]
);

$loader->register();
