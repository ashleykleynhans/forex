<?php

/**
 * Class CurrencyController
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class CurrencyController extends BaseController
{
    /**
     * Add a new currency
     */
    public function addCurrency()
    {
        $data = $this->request->getJsonRawBody();

        if (!is_object($data) || !isset($data->currency_code) || !isset($data->currency_name)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $exists = Currency::getCurrency($data->currency_code);

        if ($exists) {
            $this->sendError('CURRENCY_ALREADY_EXISTS', 400);
        }

        $currency = Currency::addCurrency($data);

        if ($currency) {
            $this->sendSuccess($currency);
        } else {
            $this->sendError('UNABLE_TO_SAVE_CURRENCY', 500);
        }
    }

    /**
     * Update an existing currency
     * @param $currencyCode
     */
    public function updateCurrency($currencyCode = null)
    {
        if (!isset($currencyCode)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $data = $this->request->getJsonRawBody();
        $currency = Currency::getCurrency($currencyCode);

        if (!$currency) {
            $this->sendError('CURRENCY_NOT_FOUND', 404);
        }

        $currency = Currency::updateCurrency($currencyCode, $data);

        if ($currency) {
            $this->sendSuccess($currencyCode);
        } else {
            $this->sendError('UNABLE_TO_UPDATE_CURRENCY', 500);
        }
    }

    /**
     * Get currency detail
     * @param null $currencyCode
     */
    public function getCurrency($currencyCode = null)
    {
        if (!isset($currencyCode)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $data = Currency::getCurrency($currencyCode);

        if ($data) {
            $this->sendSuccess($data);
        } else {
            $this->sendError('CURRENCY_NOT_FOUND', 404);
        }
    }

    /**
     * Get all available currency data
     */
    public function getAllCurrencies()
    {
        $data = Currency::getAllCurrencies();

        if ($data) {
            $this->sendSuccess($data);
        } else {
            $this->sendError('NO_CURRENCY_DATA_FOUND', 404);
        }
    }

    /**
     * Delete a currency
     * @param null $currencyCode
     */
    public function deleteCurrency($currencyCode = null)
    {
        if (!isset($currencyCode)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $currency = Currency::getCurrency($currencyCode);

        if (!$currency) {
            $this->sendError('CURRENCY_NOT_FOUND', 404);
        }

        $result = Currency::deleteCurrency($currencyCode);

        if ($result) {
            $this->sendSuccess(ResponseMessages::CURRENCY_DELETED_SUCCESSFULLY);
        } else {
            $this->sendError('UNABLE_TO_DELETE_CURRENCY', 500);
        }
    }

}
