<?php

namespace Zoho\Crm\V1\Middleware;

use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Support\UrlParameters;
use Zoho\Crm\Support\HttpMethod;

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
        $url = $query->getUrl();
        $parameters = UrlParameters::createFromUrl($url);

        // For queries with 'xmlData' URL parameter, the URL query string size might be very large.
        // For that reason we will move it to the body instead.

        if ($query->getHttpMethod() === HttpMethod::POST && $parameters->has('xmlData')) {
            $newUrl = parse_url($url, PHP_URL_PATH) . '?' . $parameters->except('xmlData');
            $query->setUrl($newUrl);
            $query->setBody((string) $parameters->only('xmlData'));
            $query->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
    }
}
