<?php

/**
 * Registering an autoloader
 */
$loader = new \Phalcon\Loader();

$loader->registerDirs(
    [
        realpath(__DIR__ . '/../models/'),
        realpath(__DIR__ . '/../controllers/'),
        realpath(__DIR__ . '/../views/')
    ]
);

$loader->register();
