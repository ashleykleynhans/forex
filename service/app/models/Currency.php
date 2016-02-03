<?php

/**
 * Class Currency
 * Currency Model
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class Currency extends \Phalcon\Mvc\Model
{
    /**
     * Initializes the model
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('currencies');
    }

    /**
     * Get an existing entry from the DB
     * @param $currencyId
     * @return mixed
     */
    public static function getCurrency($currencyId)
    {
        return self::findFirst([
            'currency_id  = :currency_id:',
            'bind' => [
                'currency_id' => $currencyId
            ]
        ]);
    }

    /**
     * Add a new currency to the DB
     * @param $data
     * @return bool
     */
    public static function addCurrency($data)
    {
        $currency = new self();

        foreach ($data as $key => $value) {
            $currency->$key = $value;
        }

        $currency->status = 'enabled';

        try {
            if ($currency->create()) {
                return $currency;
            }
        } catch (Exception $e) {
            // Do nothing, return default of false
            // @TODO: Possibly log the error
        }

        return false;
    }

    /**
     * Update a currency in the DB
     * @param $currencyId
     * @param $data
     * @return bool
     */
    public static function updateCurrency($currencyId, $data)
    {
        $currency = self::getCurrency($currencyId);

        if ($currency) {
            foreach ($data as $key => $value) {
                $currency->$key = $value;
            }

            return $currency->update();
        }

        return false;
    }

    /**
     * "Delete" a currency in the DB by setting status to disabled (soft delete)
     * @param $currencyId
     * @return bool
     */
    public static function deleteCurrency($currencyId)
    {
        $currency = self::getCurrency($currencyId);

        if ($currency) {
            $currency->status = 'disabled';
            return $currency->update();
        }

        return false;
    }
}
