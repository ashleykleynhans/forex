<?php

/**
 * Class Order
 * Order Model
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class Order extends \Phalcon\Mvc\Model
{
    /**
     * Calculate the ZAR value of the forex transaction
     * @param $value
     */
    private static function calculateRandValue($value)
    {
        $currency = Currency::getCurrency('ZAR');

        return $value * $currency->exchange_rate;
    }

    /**
     * Format currency values to 2 decmial points
     * @param $value
     * @return string
     */
    private static function formatValue($value)
    {
        return number_format(round($value, 2, PHP_ROUND_HALF_UP), 2, '.', '');
    }

    /**
     * Send an email notification
     * @param $emailAddresses
     * @param $order
     */
    private static function sendEmail($emailAddresses, $order)
    {
        $di = \Phalcon\DI::getDefault();
        $mandrill = $di['mandrill'];

        $subject = 'Forex transaction #'. $order->order_id;

        $message = [
            'subject'        => $subject,
            'from_email'     => 'noreply@ashleykleynhans.com',
            'from_name'      => 'Forex Service',
            'headers'        => ['Reply-To' => 'noreply@ashleykleynhans.com'],
            'inline_css'     => true,
            'auto_text'      => true,
            'merge_language' => 'handlebars',
            'merge_vars' => [
                [
                    'rcpt' => 'ashley.kleynhans@gmail.com',
                    'vars' => [
                        [ 'name' => 'order_id',             'content' => $order->order_id        ],
                        [ 'name' => 'currency_amount',      'content' => $order->currency_amount ],
                        [ 'name' => 'exchange_rate',        'content' => $order->exchange_rate ],
                        [ 'name' => 'surcharge_percentage', 'content' => $order->surcharge_percentage ],
                        [ 'name' => 'surcharge_amount',     'content' => $order->surcharge_amount ],
                        [ 'name' => 'payable_amount',       'content' => $order->payable_amount ],
                        [ 'name' => 'zar_amount',           'content' => $order->zar_amount ],
                    ]
                ]
            ]
        ];

        // Only add the discount to the template if there is actually a discount
        if ($order->discount_percentage > 0) {
            $message['merge_vars'][0]['vars'][] = [ 'name' => 'discount_percentage',  'content' => $order->discount_percentage ];
            $message['merge_vars'][0]['vars'][] = [ 'name' => 'discount_amount',      'content' => $order->discount_amount ];
        }

       $templateContent = [];

        $message['to'] = array_map(function($email) {
            return [
                'email' => $email
            ];
        }, $emailAddresses);

        $result = $mandrill->messages->sendTemplate('notification', $templateContent, $message, true, 'Main Pool');

        return $result;
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

        $emailAddresses = array_map(function($email) {
            return $email['email_address'];
        }, $emails);

        self::sendEmail($emailAddresses, $order);
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

        $currency = Currency::getCurrency($currencyCode);

        if (!$currency) {
            return false;
        }

        $order = new self();
        $order->currency_code = $currencyCode;
        $order->exchange_rate = $currency->exchange_rate;
        $order->surcharge_percentage = $currency->currency_surcharge;
        $order->discount_amount = 0;
        $order->discount_percentage = 0;

        if (!empty($currencyAmount)) {
            $order->currency_amount = $currencyAmount;

            // Calculate the initial currency conversion before surcharges and discounts
            $order->payable_amount = $currencyAmount * (1 / $currency->exchange_rate);

            // Calculate the surcharge
            $order->surcharge_amount = $order->payable_amount / 100 * $order->surcharge_percentage;

            // Add the surcharge to the amount payable
            $order->payable_amount += $order->surcharge_amount;
            $order->payable_amount = round($order->payable_amount, 2, PHP_ROUND_HALF_UP);
        } else {
            $order->payable_amount = $payableAmount;
            $order->surcharge_amount = $payableAmount / (100 + $order->surcharge_percentage) * $order->surcharge_percentage;
            $order->currency_amount = $payableAmount - $order->surcharge_amount;
            $order->currency_amount = $order->currency_amount * ($currency->exchange_rate);
        }

        // Apply the discount if one is available
        if ($applyDiscount && $currency->currency_discount > 0) {
            $order->discount_percentage = $currency->currency_discount;
            $order->discount_amount = $order->payable_amount / 100 * $currency->currency_discount;
            $order->payable_amount -= $order->discount_amount;
            $order->payable_amount = round($order->payable_amount, 2, PHP_ROUND_HALF_UP);
        }

        $order->zar_amount = self::calculateRandValue($order->payable_amount);
        $order->currency_amount = self::formatValue($order->currency_amount);
        $order->surcharge_amount = self::formatValue($order->surcharge_amount);
        $order->payable_amount = self::formatValue($order->payable_amount);
        $order->zar_amount = self::formatValue($order->zar_amount);

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
        $order = self::calculateOrder($currencyCode, $currencyAmount, null, true);

        if (!$order) {
            return false;
        }

        try {
            $orderResult = $order->create();
        } catch (Exception $e) {
            // Do nothing, return default of false
            // @TODO: Possibly log the error
        }

        if ($orderResult) {
            // Logic within sendEmailNotification() will determine whether or not emails should actually be sent
            self::sendEmailNotification($order);
            return $order;
        }

        return false;
    }

}
