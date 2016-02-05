<?php
/**
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */

use Phalcon\DI\FactoryDefault;
use Phalcon\Events\Manager as EventsManager;

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di['db'] = function () use ($config) {
    $adapterType = 'Phalcon\\Db\\Adapter\Pdo\\'. $config->database->adapter;

    $adapter = new $adapterType(
        [
            'host'     => $config->database->host,
            'username' => $config->database->username,
            'password' => $config->database->password,
            'dbname'   => $config->database->dbname,
        ]
    );

    if ($config->database->debug) {
        $dbListener = new DBListener();
        $eventsManager = new EventsManager();
        $eventsManager->attach('db', $dbListener);
        $adapter->setEventsManager($eventsManager);
    }

    return $adapter;
};

/**
 * Register the Guzzle Client
 */
$di->set('guzzle', function () {
    return new GuzzleHttp\Client();
});

/**
 * Models Manager for queries
 */
$di->setShared('modelsManager', function () {
    return new Phalcon\Mvc\Model\Manager();
});

/**
 * Set Config Object
 */
$di->set('config', $config);