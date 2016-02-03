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

        if (!is_object($data) || !isset($data->currency_id) || !isset($data->currency_name)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $exists = Currency::getCurrency($data->currency_id);

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
     * @param $currencyId
     */
    public function updateCurrency($currencyId = null)
    {
        if (!isset($currencyId)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $data = $this->request->getJsonRawBody();
        $currency = Currency::getCurrency($currencyId);

        if (!$currency) {
            $this->sendError('CURRENCY_NOT_FOUND', 404);
        }

        $currency = Currency::updateCurrency($currencyId, $data);

        if ($currency) {
            $this->sendSuccess($currencyId);
        } else {
            $this->sendError('UNABLE_TO_UPDATE_CURRENCY', 500);
        }
    }

    /**
     * Get currency detail
     * @param null $currencyId
     */
    public function getCurrency($currencyId = null)
    {
        if (!isset($currencyId)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $data = Currency::getCurrency($currencyId);

        if ($data) {
            $this->sendSuccess($data);
        } else {
            $this->sendError('CURRENCY_NOT_FOUND', 404);
        }
    }

    /**
     * Delete an entry
     * @param null $currencyId
     */
    public function deleteCurrency($currencyId = null)
    {
        if (!isset($currencyId)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $currency = Currency::getCurrency($currencyId);

        if (!$currency) {
            $this->sendError('CURRENCY_NOT_FOUND', 404);
        }

        $result = Currency::deleteCurrency($currencyId);

        if ($result) {
            $this->sendSuccess(ResponseMessages::CURRENCY_DELETED_SUCCESSFULLY);
        } else {
            $this->sendError('UNABLE_TO_DELETE_CURRENCY', 500);
        }
    }

}
