<?php

namespace Zoho\Crm\Api;

class RequestLauncher
{
    const BASE_URI = 'https://crm.zoho.com/crm/private/';

    private static $initialized = false;

    private static $http_client;

    private static $request_count = 0;

    public static function initialize()
    {
        if (self::$initialized) return;

        self::$http_client = new \GuzzleHttp\Client([
            'base_uri' => self::BASE_URI
        ]);

        self::$initialized = true;
    }

    public static function resetRequestCount()
    {
        self::$request_count = 0;
    }

    public static function getRequestCount()
    {
        return self::$request_count;
    }

    public static function fire(Request $request)
    {
        $response = self::$http_client->request($request->getHttpVerb(), $request->buildUri());

        self::$request_count++;

        return $response->getBody()->getContents();
    }
}

// Auto-initialization of the static class, once for all. Much handy. Such smoothness. Wow.
RequestLauncher::initialize();
