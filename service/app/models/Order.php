<?php

/**
 * Class Order
 * Order Model
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class Order extends \Phalcon\Mvc\Model
{
    /**
     * Send an email notification
     * @param $emailAddress
     * @param $order
     */
    private static function sendEmail($emailAddress, $order)
    {
        $subject = 'Your forex order #'. $order->order_id . ' completed successfully';

        $message =<<< MSG
        Thank you for your forex order #{$order->order_id}
MSG;

        mail($emailAddress, $subject, $message);
    }

    /**
     * Send email notification(s) for the order
     * @param $order
     */
    private static function sendEmailNotification($order)
    {
        $emails = Email::getEmailAddresses($order->currencyCode);

        if (!$emails) {
            return;
        }

        foreach ($emails as $email) {
            self::sendEmailNotification($email->email_address, $order);
        }
    }

    /**
     * Initializes the model
     */
    public function initialize()
    {
        $this->setSource('orders');

        $this->skipAttributesOnCreate(
            [
                'date_created'
            ]
        );
    }

    /**
     * Get an existing order from the DB
     * @param $orderId
     * @return mixed
     */
    public static function getOrder($orderId)
    {
        return self::findFirst(
            [
                'order_id  = :order_id:',
                'bind' => [ 'order_id' => $orderId ]
            ]
        );
    }

    /**
     * Add a new order to the DB
     * @param $data
     * @return bool
     */
    public static function addOrder($data)
    {
        $order = new self();

        foreach ($data as $key => $value) {
            $order->$key = $value;
        }

        try {
            if ($order->create()) {
                // Logic within sendEmailNotification() will determine whether or not emails should actually be sent
                self::sendEmailNotification($order);
                return $order;
            }
        } catch (Exception $e) {
            // Do nothing, return default of false
            // @TODO: Possibly log the error
        }

        return false;
    }

}
