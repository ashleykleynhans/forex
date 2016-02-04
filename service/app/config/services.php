<?php
/**
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */

use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileAdapter;

$di = new FactoryDefault();
/**
 * The URL component is used to generate all kind of urls in the application
 */
$di['url'] = function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri('/');

    return $url;
};

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di['db'] = function () use ($config) {
    $adapterType = 'Phalcon\\Db\\Adapter\Pdo\\'. $config->database->adapter;

    $dbSettings = [
            'host'     => $config->database->host,
            'username' => $config->database->username,
            'password' => $config->database->password,
            'dbname'   => $config->database->dbname,
    ];

    if ($config->database->adapter == 'Mysql') {
        $dbSettings['charset']  = $config->database->charset;
    }

    $adapter = new $adapterType($dbSettings);

    if ($config->database->debug) {
        $dbListener = new DBListener();
        $eventsManager = new EventsManager();
        $eventsManager->attach('db', $dbListener);
        $adapter->setEventsManager($eventsManager);
    }

    return $adapter;
};

/**
 * Register the Mandrill Library
 */
$di->set('mandrill', function () use ($config) {
    return new Mandrill($config->mandrill_api_key);
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

// Setup Logger
//$logger = new FileAdapter(__DIR__ . '/../logs/application.log');
//$di->set('logger', $logger);
