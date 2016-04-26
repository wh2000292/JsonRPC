<?php

use JsonRPC\HttpClient;

require_once __DIR__.'/../vendor/autoload.php';

class HttpClientTest extends PHPUnit_Framework_TestCase
{
    public function testWithServerError()
    {
        $this->setExpectedException('\JsonRPC\Exception\ServerErrorException');

        $httpClient = new HttpClient();
        $httpClient->handleExceptions(array(
            'HTTP/1.0 301 Moved Permanently',
            'Connection: close',
            'HTTP/1.1 500 Internal Server Error',
        ));
    }

    public function testWithConnectionFailure()
    {
        $this->setExpectedException('\JsonRPC\Exception\ConnectionFailureException');

        $httpClient = new HttpClient();
        $httpClient->handleExceptions(array(
            'HTTP/1.1 404 Not Found',
        ));
    }

    public function testWithAccessForbidden()
    {
        $this->setExpectedException('\JsonRPC\Exception\AccessDeniedException');

        $httpClient = new HttpClient();
        $httpClient->handleExceptions(array(
            'HTTP/1.1 403 Forbidden',
        ));
    }

    public function testWithAccessNotAllowed()
    {
        $this->setExpectedException('\JsonRPC\Exception\AccessDeniedException');

        $httpClient = new HttpClient();
        $httpClient->handleExceptions(array(
            'HTTP/1.0 401 Unauthorized',
        ));
    }
}
