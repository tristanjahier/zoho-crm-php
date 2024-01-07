<?php

declare(strict_types=1);

namespace Zoho\Crm;

use Http\Client\HttpAsyncClient as AsyncClientInterface;
use Http\Discovery\Exception\NotFoundException as HttpDiscoveryException;
use Http\Discovery\HttpAsyncClientDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Promise\Promise as PromiseInterface;
use Psr\Http\Client\ClientInterface;
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

    /**
     * The constructor.
     */
    public function __construct()
    {
        try {
            $this->httpClient = HttpAsyncClientDiscovery::find();

            if (! $this->httpClient instanceof ClientInterface) {
                // Force fallback to the 'catch' block, because we do not want an HTTP client
                // that supports ONLY asynchronous requests.
                throw new HttpDiscoveryException();
            }
        } catch (HttpDiscoveryException) {
            $this->httpClient = Psr18ClientDiscovery::find();
        }
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
        if (! $this->httpClient instanceof AsyncClientInterface) {
            throw new Exceptions\UnavailableHttpAsyncClientException();
        }

        return $this->httpClient->sendAsyncRequest($request)->then($onFulfilled, $onRejected);
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
