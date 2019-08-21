<?php

namespace Zoho\Crm;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Exception\RequestException;

/**
 * The request sender.
 */
class RequestSender
{
    /** @var \GuzzleHttp\Client The Guzzle client instance to make HTTP requests */
    protected $httpClient;

    /** @var int The number of API requests sent so far */
    protected $requestCount = 0;

    /** @var Preferences The client preferences container */
    protected $preferences;

    /**
     * The constructor.
     *
     * @param Preferences The client preferences container
     */
    public function __construct(Preferences $preferences)
    {
        $this->preferences = $preferences;
        $this->httpClient = new GuzzleClient();
    }

    /**
     * Send an HTTP request to the API, and return the response.
     *
     * @param \Psr\Http\Message\RequestInterface $request The request to send
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public function send(RequestInterface $request)
    {
        try {
            $response = $this->httpClient->send($request);
            $this->requestCount++;
        } catch (RequestException $e) {
            if ($this->preferences->isEnabled('exception_messages_obfuscation')) {
                // Sometimes the auth token is included in the exception message by Guzzle.
                // This exception message could end up in many "unsafe" places like server logs,
                // error monitoring services, company internal communication etc.
                // For this reason we must remove the auth token from the exception message.

                throw $this->obfuscateExceptionMessage($e);
            }

            throw $e;
        }

        return $response;
    }

    /**
     * Obfuscate an exception by removing the API auth token from its message.
     *
     * It will actually create a copy of the original exception because
     * exception messages are immutable.
     *
     * @param \GuzzleHttp\Exception\RequestException $e The exception to obfuscate
     * @return \GuzzleHttp\Exception\RequestException
     */
    private function obfuscateExceptionMessage(RequestException $e)
    {
        $pattern = '/authtoken=((?:[a-z]|\d)*)/i';

        // If the exception message does not contain sensible data, just let it through.
        if (! preg_match($pattern, $e->getMessage())) {
            return $e;
        }

        $safeMessage = preg_replace($pattern, 'authtoken=***', $e->getMessage());
        $class = get_class($e);

        return new $class(
            $safeMessage,
            $e->getRequest(),
            $e->getResponse(),
            $e->getPrevious(),
            $e->getHandlerContext()
        );
    }

    /**
     * Reset the API request counter.
     *
     * @return void
     */
    public function resetRequestCount()
    {
        $this->requestCount = 0;
    }

    /**
     * Get the number of API requests sent so far.
     *
     * @return int
     */
    public function getRequestCount()
    {
        return $this->requestCount;
    }
}
