<?php

namespace Zoho\Crm\Utils;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client as GuzzleClient;
use Zoho\Crm\Support\UrlParameters;

/**
 * Static helper class for OAuth 2.0.
 *
 * @link https://www.zoho.com/crm/developer/docs/api/oauth-overview.html
 */
final class OAuthHelper
{
    /** @var string The API OAuth 2.0 authorization endpoint used by default */
    const DEFAULT_OAUTH_ENDPOINT = 'https://accounts.zoho.com/oauth/v2/';

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
        $httpClient = new GuzzleClient();

        $parameters = new UrlParameters([
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'code' => $grantToken
        ]);

        $url = self::DEFAULT_OAUTH_ENDPOINT . 'token?' . $parameters;
        $request = new Request('POST', $url);
        $response = $httpClient->send($request);

        return json_decode((string) $response->getBody(), true);
    }
}
