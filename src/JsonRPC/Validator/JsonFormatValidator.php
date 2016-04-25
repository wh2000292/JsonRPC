<?php

namespace JsonRPC\Validator;

use JsonRPC\InvalidJsonFormat;

/**
 * Class JsonFormatValidator
 *
 * @package JsonRPC\Validator
 * @author  Frederic Guillot
 */
class JsonFormatValidator
{
    /**
     * Validate
     *
     * @static
     * @access public
     * @param  mixed $payload
     * @throws InvalidJsonFormat
     */
    public static function validate($payload)
    {
        if (! is_array($payload)) {
            throw new InvalidJsonFormat('Malformed payload');
        }
    }
}

