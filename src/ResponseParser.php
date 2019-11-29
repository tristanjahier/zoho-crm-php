<?php

namespace Zoho\Crm;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Response;
use Zoho\Crm\ResponseFormat;
use Zoho\Crm\ErrorHandler;
use Zoho\Crm\Support\Helper;

/**
 * A class to parse and transform a raw HTTP response into an API response object
 * with a clean and exploitable content.
 */
class ResponseParser
{
    /**
     * Parse an API response and transform its content into a relevant data object.
     *
     * @param \Psr\Http\Message\ResponseInterface $httpResponse The API response to read
     * @param Contracts\QueryInterface $query The origin query
     * @return Response
     */
    public function parse(HttpResponseInterface $httpResponse, QueryInterface $query)
    {
        $rawContent = (string) $httpResponse->getBody();

        $format = Helper::getUrlPathSegmentByIndex($query->getUri(), 0);
        $content = $this->parseFormattedString($rawContent, $format);

        $this->validate($content);

        if ($transformer = $query->getResponseTransformer()) {
            $content = $transformer->transformResponse($content, $query);
        }

        return new Response($query, $content, $rawContent);
    }

    /**
     * Parse a raw response body.
     *
     * @param string $content The raw response body
     * @param string $format The response format
     * @return mixed
     *
     * @throws Exceptions\UnsupportedResponseFormatException
     */
    protected function parseFormattedString(string $content, string $format)
    {
        if ($format === ResponseFormat::JSON) {
            return json_decode($content, true);
        }

        throw new Exceptions\UnsupportedResponseFormatException($format);
    }

    /**
     * Validate the readability and integrity of the response.
     *
     * @param array $content The parsed response content
     * @return void
     *
     * @throws Exceptions\UnreadableResponseException
     * @throws Exceptions\AbstractException
     */
    protected function validate($content)
    {
        if (is_null($content) || ! is_array($content)) {
            throw new Exceptions\UnreadableResponseException();
        }

        if (isset($content['response']['error'])) {
            ErrorHandler::handle($content['response']['error']);
        }
    }
}
