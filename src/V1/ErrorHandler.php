<?php

namespace Zoho\Crm\V1;

use Exception;
use GuzzleHttp\Exception\RequestException;
use Zoho\Crm\Contracts\ErrorHandlerInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Preferences;

/**
 * Handler for API v1 errors.
 */
class ErrorHandler implements ErrorHandlerInterface
{
    /** @var \Zoho\Crm\Preferences The client preferences container */
    protected $preferences;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Preferences $preferences The client preferences container
     */
    public function __construct(Preferences $preferences)
    {
        $this->preferences = $preferences;
    }

    /**
     * @inheritdoc
     */
    public function handle(Exception $exception, QueryInterface $query): void
    {
        if ($this->preferences->isEnabled('exception_messages_obfuscation')) {
            // Sometimes the auth token is included in the exception message by Guzzle.
            // This exception message could end up in many "unsafe" places like server logs,
            // error monitoring services, company internal communication etc.
            // For this reason we must remove the auth token from the exception message.

            throw $this->obfuscateExceptionMessage($exception);
        }
    }

    /**
     * Obfuscate an exception by removing the API auth token from its message.
     *
     * It will actually create a copy of the original exception because
     * exception messages are immutable.
     *
     * @param \GuzzleHttp\Exception\RequestException $exception The exception to obfuscate
     * @return \GuzzleHttp\Exception\RequestException
     */
    private function obfuscateExceptionMessage(RequestException $exception)
    {
        $pattern = '/authtoken=((?:[a-z]|\d)*)/i';

        // If the exception message does not contain sensible data, just let it through.
        if (! preg_match($pattern, $exception->getMessage())) {
            return $exception;
        }

        $safeMessage = preg_replace($pattern, 'authtoken=***', $exception->getMessage());
        $this->modifyExceptionMessage($exception, $safeMessage);

        return $exception;
    }

    /**
     * Modify the message property of an exception using reflection.
     *
     * @param \Exception $exception The exception to modify
     * @param string $newMessage The new message
     * @return void
     */
    private function modifyExceptionMessage(Exception $exception, string $newMessage)
    {
        $property = (new \ReflectionObject($exception))->getProperty('message');
        $property->setAccessible(true);
        $property->setValue($exception, $newMessage);
    }
}
