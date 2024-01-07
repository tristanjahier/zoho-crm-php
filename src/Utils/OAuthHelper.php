<?php

declare(strict_types=1);

namespace Zoho\Crm\Utils;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Zoho\Crm\Support\UrlParameters;

/**
 * Static helper class for OAuth 2.0.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/oauth-overview.html
 */
final class OAuthHelper
{
    /** @var string The API OAuth 2.0 authorization endpoint used by default */
    public const DEFAULT_OAUTH_ENDPOINT = 'https://accounts.zoho.com/oauth/v2/';

    /**
     * The constructor.
     *
     * It is private to prevent instanciation.
     */
    private function __construct()
    {
        //
    }

    /**
     * Generate the access and refresh tokens from the grant token.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/access-refresh.html
     *
     * @param string $clientId The client ID
     * @param string $clientSecret The client secret
     * @param string $redirectUri The client redirect URI
     * @param string $grantToken The grant token
     * @return array
     */
    public static function getAccessAndRefreshTokens(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $grantToken
    ) {
        $httpClient = Psr18ClientDiscovery::find();
        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();

        $parameters = new UrlParameters([
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'code' => $grantToken
        ]);

        $url = self::DEFAULT_OAUTH_ENDPOINT . 'token?' . $parameters;
        $request = $requestFactory->createRequest('POST', $url);
        $response = $httpClient->sendRequest($request);

        return json_decode((string) $response->getBody(), true);
    }
}
