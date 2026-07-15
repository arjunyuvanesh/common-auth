<?php

namespace Arjunyuvanesh\CommonAuth\Exceptions;

use Exception;

class RegistrationFailedException extends Exception
{
    /**
     * Create a new custom exception instance.
     *
     * @param string $message
     */
    public function __construct(string $message = 'Registration failed due to a database error.')
    {
        parent::__construct($message);
    }
}
