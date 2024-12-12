<?php

namespace Fincode\Laravel\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class FincodeHttpException extends HttpException
{
    /**
     * @param  string|null  $message
     * @param  int  $code
     * @return void
     */
    public function __construct(int $statusCode, $message = null, ?Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
