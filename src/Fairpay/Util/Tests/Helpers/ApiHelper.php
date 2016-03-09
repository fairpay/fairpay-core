<?php


namespace Fairpay\Util\Tests\Helpers;


class ApiHelper extends TestCaseHelper
{
    public $response;
    public $status;

    private $token;

    public function get($uri, $headers = array())
    {
        $headers = array();

        if ($this->token) {
            $headers['HTTP_Authorization'] = 'Bearer ' . $this->token;
        }

        $this->testCase->client->request('GET', 'http://api.localhost' . $uri, [], [], $headers);

        $response = $this->testCase->client->getResponse();
        $this->response = json_decode($response->getContent());
        $this->status   = $response->getStatusCode();

        $this->testCase->assertEquals('application/json', $response->headers->get('content-type'));

        if ($response->getStatusCode() >= 400) {
            $this->testCase->assertObjectHasAttribute('status', $this->response);
            $this->testCase->assertObjectHasAttribute('error', $this->response);
            $this->testCase->assertObjectHasAttribute('message', $this->response);
            $this->testCase->assertEquals($response->getStatusCode(), $this->response->status);
        }
    }

    public function setToken($token)
    {
        $this->token = $token;
    }
}