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
        $this->setSource('currencies');

        $this->skipAttributesOnCreate(
            [
                'currency_status',
                'date_created',
                'date_updated'
            ]
        );

        $this->skipAttributesOnUpdate(
            [
                'date_updated'
            ]
        );
    }

    /**
     * Get an existing entry from the DB
     * @param $currencyCode
     * @return mixed
     */
    public static function getCurrency($currencyCode)
    {
        return self::query()
            ->columns('*')
            ->leftJoin('rate', 'rate.currency_code = Currency.currency_code')
            ->where('Currency.currency_code = :currency_code:')
            ->andWhere('currency_status = :currency_status:')
            ->bind(
                [
                    'currency_code'   => $currencyCode,
                    'currency_status' => 'enabled'
                ]
            )
            ->execute()
            ->getFirst();
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
     * @param $currencyCode
     * @param $data
     * @return bool
     */
    public static function updateCurrency($currencyCode, $data)
    {
        $currency = self::getCurrency($currencyCode);

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
     * @param $currencyCode
     * @return bool
     */
    public static function deleteCurrency($currencyCode)
    {
        $currency = self::getCurrency($currencyCode);

        if ($currency) {
            $currency->status = 'disabled';
            return $currency->update();
        }

        return false;
    }
}
