<?php

namespace JsonRPC\Request;

use Exception;
use JsonRPC\Exception\InvalidJsonRpcFormatException;
use JsonRPC\ProcedureHandler;
use JsonRPC\Response\ResponseBuilder;
use JsonRPC\Validator\JsonFormatValidator;
use JsonRPC\Validator\RpcFormatValidator;

/**
 * Class RequestParser
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class RequestParser
{
    /**
     * Request payload
     *
     * @access private
     * @var mixed
     */
    private $payload;

    /**
     * ProcedureHandler
     *
     * @access private
     * @var ProcedureHandler
     */
    private $procedureHandler;

    /**
     * Get new object instance
     *
     * @static
     * @access public
     * @return RequestParser
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Set payload
     *
     * @access public
     * @param  mixed $payload
     * @return $this
     */
    public function withPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Set procedure handler
     *
     * @access public
     * @param  ProcedureHandler $procedureHandler
     * @return $this
     */
    public function withProcedureHandler(ProcedureHandler $procedureHandler)
    {
        $this->procedureHandler = $procedureHandler;
        return $this;
    }

    /**
     * Parse incoming request
     *
     * @access public
     * @return string
     */
    public function parse()
    {
        if ($this->isBatchRequest()) {
            $responses = array();

            foreach ($this->payload as $payload) {
                $responses[] = self::create()
                    ->withPayload($payload)
                    ->withProcedureHandler($this->procedureHandler)
                    ->parse();
            }

            $responses = array_filter($responses);
            return empty($responses) ? '' : '['.implode(',', $responses).']';
        }

        return $this->parseRequest();
    }

    /**
     * Parse a single request
     *
     * @access private
     * @return string
     */
    private function parseRequest()
    {
        try {

            JsonFormatValidator::validate($this->payload);
            RpcFormatValidator::validate($this->payload);

            $result = $this->procedureHandler->executeProcedure(
                $this->payload['method'],
                empty($this->payload['params']) ? array() : $this->payload['params']
            );

            if (! $this->isNotification()) {
                return ResponseBuilder::create()
                    ->withId($this->payload['id'])
                    ->withResult($result)
                    ->build();
            }
        } catch (Exception $e) {
            if ($e instanceof InvalidJsonRpcFormatException || ! $this->isNotification()) {
                return ResponseBuilder::create()
                    ->withId(isset($this->payload['id']) ? $this->payload['id'] : null)
                    ->withException($e)
                    ->build();
            }
        }

        return '';
    }

    /**
     * Return true if we have a batch request
     *
     * @access public
     * @return boolean
     */
    private function isBatchRequest()
    {
        return is_array($this->payload) && array_keys($this->payload) === range(0, count($this->payload) - 1);
    }

    /**
     * Return true if the message is a notification
     *
     * @access private
     * @return bool
     */
    private function isNotification()
    {
        return is_array($this->payload) && !isset($this->payload['id']);
    }
}
