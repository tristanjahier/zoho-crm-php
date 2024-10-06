<?php

declare(strict_types=1);

namespace Zoho\Crm\Utils;

use Zoho\Crm\HttpLayer;
use Zoho\Crm\Support\UrlParameters;

/**
 * Static helper class for OAuth 2.0.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/oauth-overview.html
 */
final class OAuthHelper
{
    /**
     * The API OAuth 2.0 authorization endpoint used by default.
     *
     * @var string
     */
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
     */
    public static function getAccessAndRefreshTokens(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $grantToken
    ): array {
        $httpLayer = new HttpLayer();

        $parameters = new UrlParameters([
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'code' => $grantToken
        ]);

        $url = self::DEFAULT_OAUTH_ENDPOINT . 'token?' . $parameters;
        $request = $httpLayer->createRequest('POST', $url);
        $response = $httpLayer->sendRequest($request);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * Revoke a refresh token.
     *
     * (It also seems to work with access tokens, even though it is undocumented.)
     *
     * @see https://www.zoho.com/crm/developer/docs/api/revoke-tokens.html
     *
     * @param string $refreshToken The refresh token to revoke
     */
    public static function revokeToken(string $refreshToken): array
    {
        $httpLayer = new HttpLayer();
        $parameters = new UrlParameters(['token' => $refreshToken]);
        $url = self::DEFAULT_OAUTH_ENDPOINT . 'token/revoke?' . $parameters;
        $request = $httpLayer->createRequest('POST', $url);
        $response = $httpLayer->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception(
                "Zoho CRM API token revoke error! Response status: {$response->getStatusCode()} {$response->getReasonPhrase()}."
            );
        }

        return json_decode((string) $response->getBody(), true);
    }
}
