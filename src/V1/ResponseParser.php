<?php

namespace Zoho\Crm\V1;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Zoho\Crm\Contracts\ResponseParserInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\ResponseFormat;
use Zoho\Crm\Response;
use Zoho\Crm\Exceptions\UnreadableResponseException;

/**
 * A class to parse and transform a raw HTTP response into an API response object
 * with a clean and exploitable content.
 */
class ResponseParser implements ResponseParserInterface
{
    /**
     * @inheritdoc
     *
     * @return Response
     */
    public function parse(HttpResponseInterface $httpResponse, QueryInterface $query): ResponseInterface
    {
        $rawContent = (string) $httpResponse->getBody();

        $format = Helper::getUrlPathSegmentByIndex($query->getUrl(), 0);
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
     * @throws \Zoho\Crm\Exceptions\UnreadableResponseException
     * @throws \Zoho\Crm\Exceptions\Api\AbstractException
     */
    protected function validate($content)
    {
        if (is_null($content) || ! is_array($content)) {
            throw new UnreadableResponseException();
        }

        if (isset($content['response']['error'])) {
            ApiErrorHandler::handle($content['response']['error']);
        }
    }
}
