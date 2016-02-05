<?php
/**
 * Phalcon CLI - executes tasks within the tasks folder
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
require_once dirname(__DIR__) . '/../vendor/autoload.php';

ini_set('display_errors', true);
date_default_timezone_set('UTC');

define('BASE_DIR', dirname(__FILE__));
define('APP_DIR', BASE_DIR);

if ($argc < 3) {
    echo 'Usage: '. $argv[0] .' <TASK> <ACTION>' . PHP_EOL;
    echo 'Usage: '. $argv[0] .' import rates' . PHP_EOL;
    exit;
}

$task = [
    'task'   => $argv[1],
    'action' => $argv[2],
];

$task['params'] = isset($argv[3]) ? array_slice($argv, 3, count($argv)) : [];

// Using the CLI factory default services container
$di = new Phalcon\DI\FactoryDefault\CLI();

// Load the configuration file
if (is_readable(APP_DIR . '/config/config_console.php')) {
    $config = include APP_DIR . '/config/config_console.php';
} else {
    throw new Exception('Unable to load config_console.php');
}

// Load the services file
if (is_readable(APP_DIR . '/config/console_services.php')) {
    include APP_DIR . '/config/console_services.php';
} else {
    throw new Exception('Unable to load config_services.php');
}

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new Phalcon\Loader();
$loader->registerDirs(
    [
        APP_DIR . '/libs/',
        APP_DIR . '/models/',
        APP_DIR . '/tasks/',
    ]
)->register();

// Create a console application
$console = new Phalcon\CLI\Console();
$console->setDI($di);

// Define global constants for the current task and action
define('CURRENT_TASK', $task['task']);
define('CURRENT_ACTION', $task['action']);

try {
    $console->handle($task);
} catch (Exception $ex) {
    echo $ex->getMessage() . PHP_EOL;
    echo $ex->getTraceAsString() . PHP_EOL;
    exit;
}