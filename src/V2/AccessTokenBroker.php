<?php

declare(strict_types=1);

namespace Zoho\Crm\V2;

use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;
use Zoho\Crm\Contracts\AccessTokenBrokerInterface;
use Zoho\Crm\Exceptions\InvalidEndpointException;
use Zoho\Crm\HttpLayer;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Support\UrlParameters;

class AccessTokenBroker implements AccessTokenBrokerInterface
{
    /**
     * The base URL of the API OAuth 2.0 authorization endpoint used by default.
     *
     * @var string
     */
    public const DEFAULT_ENDPOINT_BASE_URL = 'https://accounts.zoho.com/oauth/v2/';

    /** The OAuth 2.0 client ID */
    protected string $clientId;

    /** The OAuth 2.0 client secret */
    protected string $clientSecret;

    /** The OAuth 2.0 refresh token */
    protected string $refreshToken;

    /** The base URL of the API OAuth 2.0 authorization endpoint */
    protected string $endpointBaseUrl = self::DEFAULT_ENDPOINT_BASE_URL;

    /** The HTTP layer to make requests */
    protected HttpLayer $httpLayer;

    /**
     * The constructor.
     *
     * @param string $clientId The client ID
     * @param string $clientSecret The client secret
     * @param string $refreshToken The refresh token
     * @param string|null $endpoint (optional) The authorization endpoint base URL
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $refreshToken,
        string $endpoint = null
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->refreshToken = $refreshToken;
        $this->httpLayer = new HttpLayer();

        if (isset($endpoint)) {
            $this->setAuthorizationEndpoint($endpoint);
        }
    }

    /**
     * Set the API OAuth 2.0 authorization endpoint base URL.
     *
     * It will ensure that there is one slash at the end.
     *
     * @param string $endpoint The endpoint base URL
     */
    public function setAuthorizationEndpoint(string $endpoint): void
    {
        // Make sure the endpoint ends with a single slash
        $endpoint = Helper::finishString($endpoint, '/');

        if ($endpoint === '/') {
            throw new InvalidEndpointException();
        }

        $this->endpointBaseUrl = $endpoint;
    }

    /**
     * Get the API OAuth 2.0 authorization endpoint.
     */
    public function getAuthorizationEndpoint(): string
    {
        return $this->endpointBaseUrl;
    }

    /**
     * Request to the OAuth 2.0 authorization server to get a fresh access token.
     */
    public function requestFreshAccessToken(): ResponseInterface
    {
        $parameters = new UrlParameters([
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $this->refreshToken
        ]);

        $request = $this->httpLayer->createRequest(
            'POST',
            $this->endpointBaseUrl . 'token',
            ['Content-Type' => 'application/x-www-form-urlencoded'],
            (string) $parameters
        );

        return $this->httpLayer->sendRequest($request);
    }

    /**
     * @inheritdoc
     */
    public function getAccessTokenWithExpiryDate(): array
    {
        $response = $this->requestFreshAccessToken();
        $response = json_decode((string) $response->getBody(), true);

        $token = $response['access_token'] ?? null;
        $delayInSeconds = $response['expires_in_sec'] ?? $response['expires_in'];
        $expiryDate = (new DateTimeImmutable())->modify("+{$delayInSeconds} seconds");

        return [$token, $expiryDate];
    }
}
