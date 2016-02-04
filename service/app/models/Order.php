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
        $emails = Email::getAllEmailAddresses($order->currency_code);

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
     * Calculate a forex order
     * @param $currencyCode
     * @param null $currencyAmount
     * @param null $payableAmount
     * @param bool $applyDiscount
     * @return bool|Order
     */
    public static function calculateOrder($currencyCode, $currencyAmount = null, $payableAmount = null, $applyDiscount = false)
    {
        if (!isset($currencyAmount) && !isset($payableAmount)) {
            return false;
        }

        $currencyData = Currency::getCurrency($currencyCode);

        if (!$currencyData) {
            return false;
        }

        $order = new self();
        $order->currency_code = $currencyCode;
        $order->exchange_rate = $currencyData->rate->exchange_rate;
        $order->surcharge_percentage = $currencyData->currency->currency_surcharge;
        $order->discount_amount = 0;

        if (isset($currencyAmount)) {
            $order->currency_amount = $currencyAmount;

            // Calculate the initial currency conversion before surcharges and discounts
            $order->payable_amount = $currencyAmount * (1 / $currencyData->rate->exchange_rate);

            // Calculate the surcharge
            $order->surcharge_amount = $order->payable_amount / 100 * $order->surcharge_percentage;

            // Add the surcharge to the amount payable
            $order->payable_amount += $order->surcharge_amount;
        } else {
            $order->payable_amount = $payableAmount;

            // Calculate the surcharge amount based on the amount the buyer is prepared to pay for the transaction
            // @FIXME:
            //$order->surcharge_amount = ($payableAmount * 100 / $order->surcharge_percentage) - ($order->surcharge_percentage * 10);

            // Deduct the surcharge from the payable amount
            //$order->currency_amount = $order->payable_amount - $order->surcharge_amount;
            //$order->payable_amount = $currencyAmount * (1 / $currencyData->rate->exchange_rate);
        }

        // Apply the discount if one is available
        if ($applyDiscount && $currencyData->currency->currency_discount > 0) {
            $order->discount_amount = $order->payable_amount / 100 * $order->discount_amount;
            $order->payable_amount -= $order->discount_amount;
        }

        return $order;
    }

    /**
     * Generate a new order and save it to the DB
     *
     * While the currencies are being updated, the order being generated could
     * potentially cause the actual order amounts to differ from the quoted amount,
     * in the real world, we would first save a quote and convert the quote to an order.
     * For the assessment purposes, I'm making an assumption that its a non issue.
     *
     * @param $currencyCode
     * @paarm $currencyAmount
     * @return bool
     */
    public static function addOrder($currencyCode, $currencyAmount)
    {
        $order = self::calculateOrder($currencyCode, $currencyAmount);

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
