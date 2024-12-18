<?php

namespace Fincode\Laravel\Exceptions;

use Arr;
use Fincode\OpenAPI\ApiException;
use Fincode\OpenAPI\Model\FincodeAPIErrorResponse;
use GuzzleHttp\Exception\GuzzleException;

/**
 * FincodeAPIと通信時に発生したエラーの例外
 */
class FincodeApiException extends FincodeHttpException
{
    private array $messages = [];

    public function __construct(GuzzleException|ApiException|FincodeAPIErrorResponse $previous)
    {
        if ($previous instanceof FincodeAPIErrorResponse) {
            $previous = new ApiException('400 Bad Request', 400, [], $previous->jsonSerialize());
        }

        if ($previous instanceof ApiException) {
            $this->messages = $messages = $this->parseBody($previous->getResponseBody());

            if (count($messages) > 0) {
                $message = "{$messages[0]['error_code']}: {$messages[0]['error_message']}";
            } else {
                $message = $previous->getMessage();
            }

            parent::__construct($previous->getCode(), $message, $previous, $previous->getResponseHeaders());
        } else {
            $message = $previous->getMessage();

            parent::__construct(501, $message, $previous, $previous->getResponseHeaders(), $previous->getCode());
        }
    }

    /**
     * @param  array|object|string|null  $body  ResponseBody
     * @return array{error_code: string, error_message: string}[]
     */
    private function parseBody(array|object|string|null $body): array
    {
        $body = $body ?? [];

        if (is_object($body)) {
            $body = json_encode(json_encode($body));
        }
        if (is_string($body)) {
            $body = json_decode($body, true);
        }

        if (is_array($body) && isset($body['errors']) && is_array($body['errors'])) {
            $errors = [];
            foreach ($body['errors'] as $error) {
                $errors[] = Arr::only($error, ['error_code', 'error_message']);
            }

            return array_filter($errors);
        }

        return [];
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
