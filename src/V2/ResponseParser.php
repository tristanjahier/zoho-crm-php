<?php

namespace Zoho\Crm\V2;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Zoho\Crm\Contracts\ResponseParserInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Exceptions\UnreadableResponseException;
use Zoho\Crm\Response;

/**
 * A class to parse and transform a raw HTTP response into an API response object
 * with a clean and exploitable content.
 */
class ResponseParser implements ResponseParserInterface
{
    /**
     * @inheritdoc
     *
     * @return \Zoho\Crm\Response
     */
    public function parse(HttpResponseInterface $httpResponse, QueryInterface $query): ResponseInterface
    {
        $rawContent = (string) $httpResponse->getBody();
        $content = json_decode($rawContent, true);

        if ($content === null && ! empty(trim($rawContent))) {
            // If the decoded JSON content is null but the API response body was not empty,
            // it means that there was an error reading the response.
            throw new UnreadableResponseException();
        }

        if ($transformer = $query->getResponseTransformer()) {
            $content = $transformer->transformResponse($content, $query);
        }

        return new Response($query, $content, $rawContent);
    }
}
