<?php

namespace Fincode\Laravel\Exceptions;

use GuzzleHttp\Exception\GuzzleException;
use OpenAPI\Fincode\ApiException;

class FincodeApiException extends FincodeHttpException
{
    /**
     * @param  int  $code
     */
    public function __construct(GuzzleException|ApiException $previous, array $headers = [], $code = 0)
    {
        if ($previous instanceof ApiException) {
            $status = $previous->getCode();
            $message = $previous->getMessage();
        } else {
            $status = 501;
            $message = $previous->getMessage();
        }

        parent::__construct($status, $message, $previous, $headers, $code);
    }
}
