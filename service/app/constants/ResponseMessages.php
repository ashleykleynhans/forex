<?php

/**
 * Class ResponseMessages
 * Maps Response Codes to Human readable messages
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class ResponseMessages
{
    /**
     * Generic Error Messages
     */
    const METHOD_NOT_IMPLEMENTED = 'Method not implemented';
    const INTERNAL_SERVER_ERROR = 'Internal Server Error';
    const INVALID_PARAMS = 'Invalid Params';

    /**
     * Currency Error Messages
     */
    const CURRENCY_NOT_FOUND = 'Currency not found';
    const CURRENCY_ALREADY_EXISTS = 'Currency already exists';
    const UNABLE_TO_SAVE_CURRENCY = 'Unable to save currency';
    const UNABLE_TO_UPDATE_CURRENCY = 'Unable to update currency';
    const UNABLE_TO_DELETE_CURRENCY = 'Unable to delete currency';

    /**
     * Rate Error Messages
     */
    const RATE_NOT_FOUND = 'Rate not found';
    const RATE_ALREADY_EXISTS = 'Rate already exists';
    const UNABLE_TO_SAVE_RATE = 'Unable to save rate';
    const UNABLE_TO_UPDATE_RATE = 'Unable to update rate';
    const UNABLE_TO_DELETE_RATE = 'Unable to delete rate';

    /**
     * Email Error Messages
     */
    const EMAIL_NOT_FOUND = 'Email not found';
    const EMAIL_ALREADY_EXISTS = 'Email already exists';
    const UNABLE_TO_SAVE_EMAIL = 'Unable to save email';
    const UNABLE_TO_UPDATE_EMAIL = 'Unable to update email';
    const UNABLE_TO_DELETE_EMAIL = 'Unable to delete email';

    /**
     * Order Error Messages
     */
    const ORDER_NOT_FOUND = 'Order not found';
    const ORDER_ALREADY_EXISTS = 'Order already exists';
    const UNABLE_TO_SAVE_ORDER = 'Unable to save order';
    const UNABLE_TO_UPDATE_ORDER = 'Unable to update order';
    const UNABLE_TO_DELETE_ORDER = 'Unable to delete order';

    /**
     * Currency Success Messages
     */
    const CURRENCY_DELETED_SUCCESSFULLY = 'Currency deleted successfully';
}
