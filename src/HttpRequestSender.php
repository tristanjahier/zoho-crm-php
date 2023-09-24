<?php

namespace Zoho\Crm;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Zoho\Crm\Contracts\HttpRequestSenderInterface;

/**
 * The HTTP request sender.
 */
class HttpRequestSender implements HttpRequestSenderInterface
{
    /** @var int The number of API requests sent so far */
    protected $requestCount = 0;

    /** @var \GuzzleHttp\Client The Guzzle client instance to make HTTP requests */
    protected $httpClient;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->httpClient = new GuzzleClient();
    }

    /**
     * @inheritdoc
     *
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        $this->requestCount++;

        return $this->httpClient->send($request);
    }

    /**
     * @inheritdoc
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function sendAsync(
        RequestInterface $request,
        callable $onFulfilled,
        callable $onRejected = null
    ): PromiseInterface {
        return $this->httpClient->sendAsync($request)->then($onFulfilled, $onRejected);
    }

    /**
     * @inheritdoc
     *
     * @param \GuzzleHttp\Promise\PromiseInterface[] $promises The promises to settle
     *
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public function fetchAsyncResponses(array $promises): array
    {
        $responses = [];

        foreach ($promises as $i => $promise) {
            try {
                $responses[$i] = $promise->wait();
                $this->requestCount++;
            } catch (RequestException $e) {
                throw new Exceptions\AsyncBatchRequestException($e, $i);
            }
        }

        return $responses;
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
     * @inheritdoc
     */
    public function getRequestCount(): int
    {
        return $this->requestCount;
    }
}
