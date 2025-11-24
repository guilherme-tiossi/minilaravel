<?php

namespace App\Http\Exceptions;

use Exception;

class AppException extends Exception
{
    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }
}