<?php

namespace Zoho\Crm\Middleware;

use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Support\UrlParameters;
use Zoho\Crm\HttpVerb;

/**
 * Middleware that moves 'xmlData' parameter to body in POST requests.
 */
class XmlDataHandling implements MiddlewareInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(QueryInterface $query): void
    {
        $uri = $query->getUri();
        $parameters = UrlParameters::createFromUrl($uri);

        // For queries with 'xmlData' URL parameter, the URL query string size might be very large.
        // For that reason we will move it to the body instead.

        if ($query->getHttpVerb() === HttpVerb::POST && $parameters->has('xmlData')) {
            $newUri = parse_url($uri, PHP_URL_PATH) . '?' . $parameters->except('xmlData');
            $query->setUri($newUri);
            $query->setBody((string) $parameters->only('xmlData'));
            $query->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
    }
}
