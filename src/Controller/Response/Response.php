<?php

namespace App\ExposeApi\Controller\Response;

class Response
{
    private $response;

    private const RESPONSE = [
        'success_response' => 1,
        'error_response'   => 0,
    ];

    /**
     * Response constructor.
     *
     * @param $body
     * @param $response
     */
    public function __construct(string $body, \Klein\Response $response)
    {
        $body = json_decode($body, true);

        if ($body == self::RESPONSE['success_response']) {
            $body = $this->successMessage();
        } elseif ($body == self::RESPONSE['error_response']) {
            $body = $this->failedMessage();
        }

        if (!empty($body['errors']) && $body['errors']) {
            //  code 400 by default
            $body['code'] = $body['code'] ? $body['code'] : 400;
            $response->code($body['code']);
        }

        $this->response = $body;
    }

    /**
     * @return array
     */
    private function successMessage()
    {
        return ['message' => 'action successfully done'];
    }

    /**
     * @return array
     */
    private function failedMessage()
    {
        return ['message' => 'action failed'];
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return json_encode($this->response);
    }

    /**
     * @return array|mixed
     */
    public function body()
    {
        return $this->response;
    }
}
