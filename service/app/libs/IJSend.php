<?php

/**
 * Interface IJSend
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
interface IJSend
{

    public function sendSuccess($data);
    public function sendError($message, $code, $data = null);
    public function sendFail($data);
}
