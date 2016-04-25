<?php

namespace JsonRPC\Validator;

use JsonRPC\InvalidJsonRpcFormat;

/**
 * Class RpcFormatValidator
 *
 * @package JsonRPC\Validator
 * @author  Frederic Guillot
 */
class RpcFormatValidator
{
    /**
     * Validate
     *
     * @static
     * @access public
     * @param  array $payload
     * @throws InvalidJsonRpcFormat
     */
    public static function validate(array $payload)
    {
        if (! isset($payload['jsonrpc']) ||
            ! isset($payload['method']) ||
            ! is_string($payload['method']) ||
            $payload['jsonrpc'] !== '2.0' ||
            (isset($payload['params']) && ! is_array($payload['params']))) {

            throw new InvalidJsonRpcFormat('Invalid JSON RPC payload');
        }
    }
}

