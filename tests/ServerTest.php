<?php

use JsonRPC\Server;

require_once __DIR__.'/../vendor/autoload.php';

class ServerTest extends PHPUnit_Framework_TestCase
{
    private $payload = '{"jsonrpc": "2.0", "method": "sum", "params": [1,2,4], "id": "1"}';

    public function testCustomAuthenticationHeader()
    {
        $env = array(
            'HTTP_X_AUTH' => base64_encode('myuser:mypassword'),
        );

        $server = new Server($this->payload, $env);
        $server->setAuthenticationHeader('X-Auth');
        $this->assertEquals('myuser', $server->getUsername());
        $this->assertEquals('mypassword', $server->getPassword());
    }

    public function testCustomAuthenticationHeaderWithEmptyValue()
    {
        $server = new Server($this->payload, array());
        $server->setAuthenticationHeader('X-Auth');
        $this->assertNull($server->getUsername());
        $this->assertNull($server->getPassword());
    }

    public function testGetUsername()
    {
        $server = new Server($this->payload, array());
        $this->assertNull($server->getUsername());

        $server = new Server($this->payload, array('PHP_AUTH_USER' => 'username'));
        $this->assertEquals('username', $server->getUsername());
    }

    public function testGetPassword()
    {
        $server = new Server($this->payload, array());
        $this->assertNull($server->getPassword());

        $server = new Server($this->payload, array('PHP_AUTH_PW' => 'password'));
        $this->assertEquals('password', $server->getPassword());
    }
}
