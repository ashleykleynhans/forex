<?php

/**
 * Class OrderController
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class OrderController extends BaseController
{
    /**
     * Create a new forex order
     */
    public function addOrder()
    {
        $data = $this->request->getJsonRawBody();

        if (!is_object($data) || !isset($data->currency_code) || !isset($data->currency_amount)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $order = Order::addOrder($data->currency_code, $data->currency_amount);

        if ($order) {
            $this->sendSuccess($order);
        } else {
            $this->sendError('UNABLE_TO_SAVE_ORDER', 500);
        }
    }

    /**
     * Get a quote for a forex order
     */
    public function getQuote()
    {
        $data = $this->request->getJsonRawBody();

        if (!is_object($data) || empty($data->currency_code) || (empty($data->currency_amount) && empty($data->payable_amount))) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $quote = Order::calculateOrder($data->currency_code, $data->currency_amount, $data->payable_amount, false);

        if ($quote) {
            $this->sendSuccess($quote);
        } else {
            $this->sendError('UNABLE_TO_GET_QUOTE', 500);
        }
    }

}
