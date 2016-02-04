<?php

/**
 * Class RateController
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class RateController extends BaseController
{
    /**
     * Add a new exchange rate
     */
    public function addRate()
    {
        $data = $this->request->getJsonRawBody();

        if (!is_object($data) || !isset($data->currency_code) || !isset($data->currency_name)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $exists = Rate::getRate($data->currency_code);

        if ($exists) {
            $this->sendError('RATE_ALREADY_EXISTS', 400);
        }

        $rate = Rate::addRate($data);

        if ($rate) {
            $this->sendSuccess($rate);
        } else {
            $this->sendError('UNABLE_TO_SAVE_RATE', 500);
        }
    }

    /**
     * Update an existing exchange rate
     * @param $currencyCode
     */
    public function updateRate($currencyCode = null)
    {
        if (!isset($currencyCode)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $data = $this->request->getJsonRawBody();
        $rate = Rate::getRate($currencyCode);

        if (!$rate) {
            $this->sendError('RATE_NOT_FOUND', 404);
        }

        $rate = Rate::updateRate($currencyCode, $data);

        if ($rate) {
            $this->sendSuccess($currencyCode);
        } else {
            $this->sendError('UNABLE_TO_UPDATE_RATE', 500);
        }
    }

    /**
     * Get exchange rate detail
     * @param null $currencyCode
     */
    public function getRate($currencyCode = null)
    {
        if (!isset($currencyCode)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $data = Rate::getRate($currencyCode);

        if ($data) {
            $this->sendSuccess($data);
        } else {
            $this->sendError('RATE_NOT_FOUND', 404);
        }
    }

}
