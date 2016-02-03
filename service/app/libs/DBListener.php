<?php

use Phalcon\Logger\Adapter\File as Logger;

/**
 * Class DBListener
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class DBListener
{

    protected $_logger;

    public function __construct()
    {
        $this->_logger = new Logger(realpath(__DIR__ . '/../logs') . '/sql.log');
    }

    public function afterQuery($event, $connection)
    {
        $this->_logger->log($connection->getSQLStatement(), \Phalcon\Logger::INFO);
    }

}
