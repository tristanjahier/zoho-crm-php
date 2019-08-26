<?php

namespace Zoho\Crm;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Zoho\Crm\Api\Query;
use Zoho\Crm\Api\Response;
use Zoho\Crm\Api\ResponseFormat;
use Zoho\Crm\Api\ErrorHandler;
use Zoho\Crm\Support\Helper;

/**
 * A class to transform a raw HTTP response into an API response object
 * with a clean and exploitable content.
 */
class ResponseTransformer
{
    /**
     * Parse an API response and transform its content into a relevant data object.
     *
     * @param \Psr\Http\Message\ResponseInterface $httpResponse The API response to read
     * @param Api\Query $query The origin query
     * @return Api\Response
     */
    public function transform(HttpResponseInterface $httpResponse, Query $query)
    {
        $rawContent = $httpResponse->getBody()->getContents();

        $parsedContent = $this->parse($rawContent, $query->getFormat());

        $this->validate($parsedContent);

        $content = $this->clean($parsedContent, $query);

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
    private function parse(string $content, string $format)
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
     * @throws Api\Exceptions\AbstractException
     */
    private function validate($content)
    {
        if (is_null($content) || ! is_array($content)) {
            throw new Exceptions\UnreadableResponseException();
        }

        if (isset($content['response']['error'])) {
            ErrorHandler::handle($content['response']['error']);
        }
    }

    /**
     * Clean up the raw response, get rid of metadata, simplify the data structure.
     *
     * @param array $content The raw parsed response content
     * @param Api\Query $query The origin query
     * @return mixed
     *
     * @throws Exceptions\MethodNotFoundException
     */
    private function clean($content, Query $query)
    {
        $apiMethodHandler = $query->getClientMethod();

        if ($apiMethodHandler->responseContainsData($content, $query)) {
            return $apiMethodHandler->tidyResponse($content, $query);
        } else {
            return null; // No data
        }
    }
}
