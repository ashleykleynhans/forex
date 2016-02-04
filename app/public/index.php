<?php
require dirname(__DIR__) . '/../vendor/autoload.php';

date_default_timezone_set('UTC');

error_reporting(E_ALL);
//error_reporting(0);
ini_set('display_errors', 1);

try {

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../app/config/config.php";

    /**
     * Read auto-loader
     */
    include __DIR__ . "/../app/config/loader.php";

    /**
     * Read services
     */
    include __DIR__ . "/../app/config/services.php";

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    $application->useImplicitView(false);

    echo $application->handle()->getContent();

} catch (\Exception $e) {
    echo $e->getMessage();
}
