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
     * Currency Success Messages
     */
    const CURRENCY_DELETED_SUCCESSFULLY = 'Currency deleted successfully';
}
