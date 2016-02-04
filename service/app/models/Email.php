<?php

/**
 * Class Email
 * Email Model
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class Email extends \Phalcon\Mvc\Model
{
    /**
     * Initializes the model
     */
    public function initialize()
    {
        $this->setSource('order_emails');

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
     * Get an existing email address from the DB
     * @param $currencyCode
     * @param $emailAddress
     * @return mixed
     */
    public static function getEmailAddress($currencyCode, $emailAddress)
    {
        return self::findFirst(
            [
                'currency_code = :currency_code:',
                'email_address = :email_address:',
                'bind' => [
                    'currency_code' => $currencyCode,
                    'email_address' => $emailAddress,
                    'email_status'  => 'enabled'
                ]
            ]
        );
    }

    /**
     * Get all email addresses for a specific currency from the DB
     * @param $currencyCode
     * @return mixed
     */
    public static function getAllEmailAddresses($currencyCode)
    {
        return self::query()
            ->columns('*')
            ->where('currency_code = :currency_code:')
            ->andwhere('email_status = :email_status:')
            ->bind(
                [
                    'currency_code' => $currencyCode,
                    'email_status'  => 'enabled'
                ]
            )
            ->execute();
    }

    /**
     * Add a new email address to the DB
     * @param $data
     * @return bool
     */
    public static function addEmailAddress($data)
    {
        $email = new self();

        foreach ($data as $key => $value) {
            $email->$key = $value;
        }

        try {
            if ($email->create()) {
                return $email;
            }
        } catch (Exception $e) {
            // Do nothing, return default of false
            // @TODO: Possibly log the error
        }

        return false;
    }

    /**
     * Update an email address in the DB
     * @param $currencyCode
     * @param $emailAddress
     * @param $data
     * @return bool
     */
    public static function updateEmailAddress($currencyCode, $emailAddress, $data)
    {
        $email = self::getEmailAddress($currencyCode, $emailAddress);

        if ($email) {
            foreach ($data as $key => $value) {
                $email->$key = $value;
            }

            return $email->update();
        }

        return false;
    }

    /**
     * "Delete" an email address in the DB by setting status to disabled (soft delete)
     * @param $currencyCode
     * @param $emailAddress
     * @return bool
     */
    public static function deleteEmailAddress($currencyCode, $emailAddress)
    {
        $email = self::getEmailAddress($currencyCode, $emailAddress);

        if ($email) {
            $email->status = 'disabled';
            return $email->update();
        }

        return false;
    }

}
