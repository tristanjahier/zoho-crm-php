<?php

declare(strict_types=1);

namespace Zoho\Crm;

use Http\Client\HttpAsyncClient as AsyncClientInterface;
use Http\Discovery\Exception\NotFoundException as HttpDiscoveryException;
use Http\Discovery\HttpAsyncClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Promise\Promise as PromiseInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Zoho\Crm\Contracts\HttpLayerInterface;

/**
 * The HTTP layer.
 */
class HttpLayer implements HttpLayerInterface
{
    /** The number of API requests sent so far */
    protected int $requestCount = 0;

    /** The HTTP client to make requests */
    protected ClientInterface $httpClient;

    /** The PSR-17 request factory */
    protected RequestFactoryInterface $requestFactory;

    /** The PSR-17 stream factory */
    protected StreamFactoryInterface $streamFactory;

    /**
     * The constructor.
     *
     * @param \Psr\Http\Client\ClientInterface $client A PSR-18 HTTP client
     * @param \Psr\Http\Message\RequestFactoryInterface $requestFactory A PSR-17 HTTP request factory
     * @param \Psr\Http\Message\StreamFactoryInterface $streamFactory A PSR-17 HTTP stream factory
     */
    public function __construct(
        ClientInterface $client = null,
        RequestFactoryInterface $requestFactory = null,
        StreamFactoryInterface $streamFactory = null
    ) {
        $this->httpClient = $client ?? $this->findBestPsr18Client();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * @inheritdoc
     */
    public function createRequest(string $method, string $url, array $headers = [], string $body = null): RequestInterface
    {
        $request = $this->requestFactory->createRequest($method, $url);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if (isset($body)) {
            $request = $request->withBody($this->streamFactory->createStream($body));
        }

        return $request;
    }

    /**
     * @inheritdoc
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requestCount++;

        return $this->httpClient->sendRequest($request);
    }

    /**
     * @inheritdoc
     *
     * @return \Http\Promise\Promise
     */
    public function sendAsyncRequest(
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
     * Find the best HTTP client compliant with PSR-18, with asynchronous requests support if possible.
     *
     * @return \Psr\Http\Client\ClientInterface
     */
    protected function findBestPsr18Client(): ClientInterface
    {
        try {
            $httpClient = HttpAsyncClientDiscovery::find();

            if (! $httpClient instanceof ClientInterface) {
                // Force fallback to the 'catch' block, because we do not want an HTTP client
                // that supports ONLY asynchronous requests.
                throw new HttpDiscoveryException();
            }
        } catch (HttpDiscoveryException) {
            $httpClient = Psr18ClientDiscovery::find();
        }

        return $httpClient;
    }

    /**
     * Reset the API request counter.
     *
     * @return void
     */
    public function resetRequestCount(): void
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
