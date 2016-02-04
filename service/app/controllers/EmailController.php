<?php

/**
 * Class EmailController
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class EmailController extends BaseController
{
    /**
     * Add a new email address for notifications of orders of a particular exchange rate
     */
    public function addEmailAddress()
    {
        $data = $this->request->getJsonRawBody();

        if (!is_object($data) || !isset($data->currency_code) || !isset($data->email_address)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $exists = Email::getEmailAddress($data->currency_code, $data->email_address);

        if ($exists) {
            $this->sendError('EMAIL_ALREADY_EXISTS', 400);
        }

        $email = Email::addEmailAddress($data);

        if ($email) {
            $this->sendSuccess($email);
        } else {
            $this->sendError('UNABLE_TO_SAVE_EMAIL', 500);
        }
    }

    /**
     * Update an existing email address
     * @param $currencyCode
     * @param $emailAddress
     */
    public function updateEmailAddress($currencyCode = null, $emailAddress = null)
    {
        if (!isset($currencyCode) || !isset($emailAddress)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $data = $this->request->getJsonRawBody();
        $email = Email::getEmailAddress($currencyCode, $emailAddress);

        if (!$email) {
            $this->sendError('EMAIL_NOT_FOUND', 404);
        }

        $email = Email::updateEmailAddress($currencyCode, $emailAddress, $data);

        if ($email) {
            $this->sendSuccess(
                [
                    'currency_code' => $currencyCode,
                    'email_address' => $emailAddress
                ]
            );
        } else {
            $this->sendError('UNABLE_TO_UPDATE_EMAIL', 500);
        }
    }

    /**
     * Get email address
     * @param null $currencyCode
     * @param null $emailAddress
     */
    public function getEmailAddress($currencyCode = null, $emailAddress = null)
    {
        if (!isset($currencyCode) || !isset($emailAddress)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $data = Email::getEmailAddress($currencyCode, $emailAddress);

        if ($data) {
            $this->sendSuccess($data);
        } else {
            $this->sendError('EMAIL_NOT_FOUND', 404);
        }
    }

    /**
     * Delete an email address
     * @param null $currencyCode
     * @param null $emailAddress
     */
    public function deleteEmailAddress($currencyCode = null, $emailAddress = null)
    {
        if (!isset($currencyCode) || !isset($emailAddress)) {
            $this->sendError('INVALID_PARAMS', 400);
        }

        $email = Email::getEmailAddress($currencyCode, $emailAddress);

        if (!$email) {
            $this->sendError('EMAIL_NOT_FOUND', 404);
        }

        $result = Currency::deleteCurrency($currencyCode);

        if ($result) {
            $this->sendSuccess(ResponseMessages::CURRENCY_DELETED_SUCCESSFULLY);
        } else {
            $this->sendError('UNABLE_TO_DELETE_CURRENCY', 500);
        }
    }

}
