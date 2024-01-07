<?php

declare(strict_types=1);

namespace Zoho\Crm;

use Http\Discovery\HttpAsyncClientDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Promise\Promise as PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zoho\Crm\Contracts\HttpRequestSenderInterface;

/**
 * The HTTP request sender.
 */
class HttpRequestSender implements HttpRequestSenderInterface
{
    /** @var int The number of API requests sent so far */
    protected $requestCount = 0;

    /** @var \Psr\Http\Client\ClientInterface The HTTP client to make requests */
    protected $httpClient;

    /** @var \Http\Client\HttpAsyncClient The HTTP client to make asynchronous requests */
    protected $httpAsyncClient;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->httpClient = Psr18ClientDiscovery::find();
        $this->httpAsyncClient = HttpAsyncClientDiscovery::find();
    }

    /**
     * @inheritdoc
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        $this->requestCount++;

        return $this->httpClient->sendRequest($request);
    }

    /**
     * @inheritdoc
     *
     * @return \Http\Promise\Promise
     */
    public function sendAsync(
        RequestInterface $request,
        callable $onFulfilled,
        callable $onRejected = null
    ): PromiseInterface {
        return $this->httpAsyncClient->sendAsyncRequest($request)->then($onFulfilled, $onRejected);
    }

    /**
     * @inheritdoc
     *
     * @param \Http\Promise\Promise[] $promises The promises to settle
     *
     * @throws \Zoho\Crm\Exceptions\AsyncBatchRequestException
     */
    public function fetchAsyncResponses(array $promises): array
    {
        $responses = [];

        foreach ($promises as $i => $promise) {
            try {
                $responses[$i] = $promise->wait();
                $this->requestCount++;
            } catch (\Throwable $e) {
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
