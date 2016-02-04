<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\DI\FactoryDefault;
use Phalcon\Events\Manager as EventsManager;

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
 * Register the Guzzle Client
 */
$di->set('guzzleClient', function () {
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