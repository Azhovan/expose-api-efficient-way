<?php

namespace Tests;

use App\ExposeApi\Controller\Response\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    /**
     * @var string
     */
    private const SUCCESS_ACTION = 1;
    private const ERROR_ACTION = 0;
    /**
     * @var \Klein\Response
     */
    private $mockResponse;
    /**
     * @var Response
     */
    private $responseObject;

    public function setUp()
    {
        parent::setUp();

        $this->mockResponse = $this->createMock(\Klein\Response::class);
    }

    /**
     * @expectedException \TypeError
     */
    public function test_body_should_be_string()
    {
        $this->responseObject = new Response([], $this->mockResponse);
    }

    /**
     * @expectedException \TypeError
     */
    public function test_response_object_should_be_available()
    {
        $this->responseObject = new Response([]);
    }

    /**
     * @expectedException \TypeError
     */
    public function test_response_object_constructor()
    {
        $this->responseObject = new Response();
    }

    public function test_if_response_is_true_return_success_message()
    {
        $this->responseObject = new Response(self::SUCCESS_ACTION, $this->mockResponse);
        $this->assertSame(['message' => 'action successfully done'], $this->responseObject->body());
    }

    public function test_if_response_is_false_return_error_message()
    {
        $this->responseObject = new Response(self::ERROR_ACTION, $this->mockResponse);
        $this->assertSame(['message' => 'action failed'], $this->responseObject->body());
    }

    public function test_if_body_contains_error_response_should_contains_error_code()
    {
        $body = json_encode(
            [
                'code'    => 500,
                'errors'  => true,
                'message' => 'this is error message',
            ]
        );

        $this->responseObject = new Response($body, $this->mockResponse);
        $this->assertSame(500, $this->responseObject->body()['code']);
    }

    public function test_if_body_does_not_contains_error_code_response_should_contains_default_error_code_400()
    {
        $body = json_encode(
            [
                'errors'  => true,
                'message' => 'this is error message',
            ]
        );

        $this->responseObject = new Response($body, $this->mockResponse);
        $this->assertSame(400, $this->responseObject->body()['code']);
    }

    public function test_response_should_be_string()
    {
        $body = '{"name":"1b","prepTime":"2b","vegetarian":false,"difficulty":"3b","id":"4df604aa-0693-4d8e-ad26-6f3eca4d81b6"}';
        $this->responseObject = new Response($body, $this->mockResponse);

        $this->assertTrue(is_string($this->responseObject->get()));
    }

    public function test_response_should_be_valid_json()
    {
        $body = '{"name":"1b","prepTime":"2b","vegetarian":false,"difficulty":"3b","id":"4df604aa-0693-4d8e-ad26-6f3eca4d81b6"}';
        $this->responseObject = new Response($body, $this->mockResponse);

        $response = $this->responseObject->get();
        $error = json_last_error();

        $this->assertSame($error, JSON_ERROR_NONE);
    }

    public function test_input_should_be_valid_json()
    {
        $invalidBody = '{"name":"1b\","prepTime":"2b","vegetarian":false,;"difficulty":"3b",,"id":"4df604aa-0693-4d8e-ad26-6f3eca4d81b6"}';
        $this->responseObject = new Response($invalidBody, $this->mockResponse);

        $response = $this->responseObject->get();
        $error = json_last_error();

        $this->assertFalse((bool) $error);
    }
}
