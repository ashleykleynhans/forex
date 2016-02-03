<?php
/**
 * Class BaseController
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class BaseController extends \Phalcon\Mvc\Controller implements IJSend
{
    public function onConstruct()
    {
        $this->response->setContentType('application/json');
    }

    /**
     * Send a success response if an API call was successful
     * @param $data
     */
    public function sendSuccess($data)
    {
        $this->response->setStatusCode(200, HttpStatusCodes::getMessage(200))->sendHeaders();

        $this->response->setJsonContent([
            'status' => 'success',
            'data'   => $data
        ]);

        if (!$this->response->isSent()) {
            $this->response->send();
        }
    }

    /**
     * Send an error response if an API call failed
     * @param $errorCode
     * @param int $httpStatusCode
     * @param null $data
     */
    public function sendError($errorCode, $httpStatusCode = 500, $data = null)
    {
        $response = [
            'status'  => 'error',
            'message' => constant('ResponseMessages::'. $errorCode),
            'code'    => $errorCode
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        $this->response->setStatusCode($httpStatusCode, HttpStatusCodes::getMessage($httpStatusCode))->sendHeaders();
        $this->response->setJsonContent($response);

        if (!$this->response->isSent()) {
            $this->response->send();
        }

        // Prevent further processing once an error is returned
        exit;
    }

    /**
     * Send a fail response if an API call failed
     * @param $data
     * @param int $httpStatusCode
     */
    public function sendFail($data, $httpStatusCode = 500)
    {
        $this->response->setStatusCode($httpStatusCode, HttpStatusCodes::getMessage($httpStatusCode))->sendHeaders();

        $this->response->setJsonContent([
            'status' => 'fail',
            'data'   => $data
        ]);

        if (!$this->response->isSent()) {
            $this->response->send();
        }
    }

}
