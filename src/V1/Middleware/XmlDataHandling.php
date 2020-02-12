<?php

namespace Zoho\Crm\V1\Middleware;

use Zoho\Crm\Contracts\MiddlewareInterface;
use Zoho\Crm\Contracts\QueryInterface;
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
        // For queries with 'xmlData' URL parameter, the URL query string size might be very large.
        // For that reason we will move it to the body instead.

        if ($query->getHttpMethod() === HttpMethod::POST && $query->hasUrlParameter('xmlData')) {
            $query->setBody((string) $query->getUrlParameters()->only('xmlData'));
            $query->removeUrlParameter('xmlData');
            $query->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
    }
}
