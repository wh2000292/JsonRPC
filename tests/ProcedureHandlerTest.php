<?php

use JsonRPC\ProcedureHandler;

require_once __DIR__.'/../vendor/autoload.php';

class A
{
    public function getAll($p1, $p2, $p3 = 4)
    {
        return $p1 + $p2 + $p3;
    }
}

class B
{
    public function getAll($p1)
    {
        return $p1 + 2;
    }
}

class ProcedureHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException BadFunctionCallException
     */
    public function testProcedureNotFound()
    {
        $server = new ProcedureHandler;
        $server->executeProcedure('a');
    }

    /**
     * @expectedException BadFunctionCallException
     */
    public function testCallbackNotFound()
    {
        $server = new ProcedureHandler;
        $server->withCallback('b', function() {});
        $server->executeProcedure('a');
    }

    /**
     * @expectedException BadFunctionCallException
     */
    public function testClassNotFound()
    {
        $server = new ProcedureHandler;
        $server->withClassAndMethod('getAllTasks', 'c', 'getAll');
        $server->executeProcedure('getAllTasks');
    }

    /**
     * @expectedException BadFunctionCallException
     */
    public function testMethodNotFound()
    {
        $server = new ProcedureHandler;
        $server->withClassAndMethod('getAllTasks', 'A', 'getNothing');
        $server->executeProcedure('getAllTasks');
    }

    public function testIsPositionalArguments()
    {
        $server = new ProcedureHandler;
        $this->assertFalse($server->isPositionalArguments(
            array('a' => 'b', 'c' => 'd')
        ));

        $server = new ProcedureHandler;
        $this->assertTrue($server->isPositionalArguments(
            array('a', 'b', 'c')
        ));
    }

    public function testBindNamedArguments()
    {
        $server = new ProcedureHandler;
        $server->withClassAndMethod('getAllA', 'A', 'getAll');
        $server->withClassAndMethod('getAllB', 'B', 'getAll');
        $server->withClassAndMethod('getAllC', new B, 'getAll');
        $this->assertEquals(6, $server->executeProcedure('getAllA', array('p2' => 4, 'p1' => -2)));
        $this->assertEquals(10, $server->executeProcedure('getAllA', array('p2' => 4, 'p3' => 8, 'p1' => -2)));
        $this->assertEquals(6, $server->executeProcedure('getAllB', array('p1' => 4)));
        $this->assertEquals(5, $server->executeProcedure('getAllC', array('p1' => 3)));
    }

    public function testBindPositionalArguments()
    {
        $server = new ProcedureHandler;
        $server->withClassAndMethod('getAllA', 'A', 'getAll');
        $server->withClassAndMethod('getAllB', 'B', 'getAll');
        $this->assertEquals(6, $server->executeProcedure('getAllA', array(4, -2)));
        $this->assertEquals(2, $server->executeProcedure('getAllA', array(4, 0, -2)));
        $this->assertEquals(4, $server->executeProcedure('getAllB', array(2)));
    }

    public function testRegisterNamedArguments()
    {
        $server = new ProcedureHandler;
        $server->withCallback('getAllA', function($p1, $p2, $p3 = 4) {
            return $p1 + $p2 + $p3;
        });

        $this->assertEquals(6, $server->executeProcedure('getAllA', array('p2' => 4, 'p1' => -2)));
        $this->assertEquals(10, $server->executeProcedure('getAllA', array('p2' => 4, 'p3' => 8, 'p1' => -2)));
    }

    public function testRegisterPositionalArguments()
    {
        $server = new ProcedureHandler;
        $server->withCallback('getAllA', function($p1, $p2, $p3 = 4) {
            return $p1 + $p2 + $p3;
        });

        $this->assertEquals(6, $server->executeProcedure('getAllA', array(4, -2)));
        $this->assertEquals(2, $server->executeProcedure('getAllA', array(4, 0, -2)));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testTooManyArguments()
    {
        $server = new ProcedureHandler;
        $server->withClassAndMethod('getAllC', new B, 'getAll');
        $server->executeProcedure('getAllC', array('p1' => 3, 'p2' => 5));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNotEnoughArguments()
    {
        $server = new ProcedureHandler;
        $server->withClassAndMethod('getAllC', new B, 'getAll');
        $server->executeProcedure('getAllC');
    }
}
