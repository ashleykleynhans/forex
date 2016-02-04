<?php
require_once dirname(__DIR__) . '/../vendor/autoload.php';

use Phalcon\Mvc\Micro\Collection as MicroCollection;

error_reporting(E_ALL);

date_default_timezone_set('UTC');

try {
    /**
     * Read the configuration
     */

    //error_reporting(0);
    ini_set('display_errors', 1);

    $config = include __DIR__ . '/../app/config/config.php';

    /**
     * Read auto-loader
     */
    include __DIR__ . '/../app/config/loader.php';

    /**
     * Read services
     */
    include __DIR__ . '/../app/config/services.php';

    /**
     * Create Application
     */
    $app = new \Phalcon\Mvc\Micro($di);

    /**
     * Add your routes here
     */

    /**
     * Expose the /v1/currency end point
     */
    $currency = new MicroCollection();
    $currency->setHandler('CurrencyController', true);
    $currency->setPrefix('/v1/currency');
    $currency->post('/', 'addCurrency');
    $currency->put('/{code}', 'updateCurrency');
    $currency->get('/{code}', 'getCurrency');
    $currency->delete('/{code}', 'deleteCurrency');
    $app->mount($currency);

    /**
     * Expose the /v1/rates end point
     */
    $rates = new MicroCollection();
    $rates->setHandler('RateController', true);
    $rates->setPrefix('/v1/rates');
    $rates->post('/', 'addRate');
    $rates->put('/{code}', 'updateRate');
    $rates->get('/{code}', 'getRate');
    $app->mount($rates);

    /**
     * Expose the /v1/emails end point
     */
    $emails = new MicroCollection();
    $emails->setHandler('EmailController', true);
    $emails->setPrefix('/v1/emails');
    $emails->post('/', 'addEmailAddress');
    $emails->put('/{code}/{email}', 'updateEmailAddress');
    $emails->get('/{code}/{email}', 'getEmailAddress');
    $app->mount($emails);

    /**
     * Expose the /v1/orders end point
     */
    $orders = new MicroCollection();
    $orders->setHandler('OrderController', true);
    $orders->setPrefix('/v1/orders');
    $orders->post('/', 'addOrder');
    $app->mount($orders);

    /**
     * Not found handler
     */
    $app->notFound(function () use ($app) {
        $app->response->setStatusCode(404, HttpStatusCodes::getMessage(404))->sendHeaders();
        $app->response->setContentType('application/json');
        $app->response->setJsonContent([
            'status'  => 'error',
            'message' => ResponseMessages::METHOD_NOT_IMPLEMENTED,
            'code'    => 'METHOD_NOT_IMPLEMENTED'
        ]);
        $app->response->send();
    });

    $app->handle();
} catch (\Exception $e) {
    $app->response->setStatusCode(500, HttpStatusCodes::getMessage(500))->sendHeaders();

    echo json_encode([
        'status'  => 'error',
        'message' => ResponseMessages::INTERNAL_SERVER_ERROR,
        'code'    => 'INTERNAL_SERVER_ERROR',
        'ex'      => $e->getMessage()
    ]);
};
