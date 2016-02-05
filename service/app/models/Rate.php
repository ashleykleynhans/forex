<?php

/**
 * Class Rate
 * Rate Model
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class Rate extends \Phalcon\Mvc\Model
{
    /**
     * Initializes the model
     */
    public function initialize()
    {
        $this->setSource('rates');

        $this->skipAttributesOnCreate(
            [
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
     * Get an existing exchange rate from the DB
     * @param $currencyCode
     * @return mixed
     */
    public static function getRate($currencyCode)
    {
        return self::findFirst(
            [
                'currency_code  = :currency_code:',
                'bind' => [ 'currency_code' => $currencyCode ]
            ]
        );
    }

    /**
     * Add a new exchange rate to the DB
     * @param $data
     * @return bool
     */
    public static function addRate($data)
    {
        $rate = new self();

        foreach ($data as $key => $value) {
            $rate->$key = $value;
        }

        try {
            if ($rate->create()) {
                return $rate;
            }
        } catch (Exception $e) {
            // Do nothing, return default of false
            // @TODO: Possibly log the error
        }

        return false;
    }

    /**
     * Update an exchange rate in the DB
     * @param $currencyCode
     * @param $exchangeRate
     * @return bool
     */
    public static function updateRate($currencyCode, $exchangeRate)
    {
        $rate = self::getRate($currencyCode);

        if ($rate) {
            $rate->exchange_rate = $exchangeRate;

            return $rate->update();
        }

        return false;
    }

}
