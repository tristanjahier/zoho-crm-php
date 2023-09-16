<?php

namespace Zoho\Crm\V2;

use Exception;
use Zoho\Crm\Contracts\ErrorHandlerInterface;
use Zoho\Crm\Contracts\RequestInterface;

/**
 * Handler for API v2 errors.
 */
class ErrorHandler implements ErrorHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function handle(Exception $exception, RequestInterface $request): void
    {
        if ($exception instanceof \GuzzleHttp\Exception\ClientException) {
            if ($exception->getCode() === 401) {
                $response = json_decode((string) $exception->getResponse()->getBody(), true);

                if ($response['code'] === 'INVALID_TOKEN') {
                    throw new Exceptions\InvalidTokenException($exception->getMessage());
                } elseif ($response['code'] === 'AUTHENTICATION_FAILURE') {
                    throw new Exceptions\AuthenticationFailureException($exception->getMessage());
                }
            }
        }
    }
}
