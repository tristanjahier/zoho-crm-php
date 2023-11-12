<?php

declare(strict_types=1);

namespace Zoho\Crm\V2;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\ResponseInterface;
use Zoho\Crm\Contracts\ResponseParserInterface;
use Zoho\Crm\Exceptions\UnreadableResponseException;
use Zoho\Crm\Response;

/**
 * A class to parse and transform a raw HTTP response into an API response object
 * with a clean and exploitable content.
 */
class ResponseParser implements ResponseParserInterface
{
    /** @var Preferences The client preferences */
    protected $preferences;

    /**
     * The constructor.
     *
     * @param Preferences $preferences The client preferences
     */
    public function __construct(Preferences $preferences)
    {
        $this->preferences = $preferences;
    }

    /**
     * @inheritdoc
     *
     * @return \Zoho\Crm\Response
     */
    public function parse(HttpResponseInterface $httpResponse, RequestInterface $request): ResponseInterface
    {
        $rawContent = (string) $httpResponse->getBody();
        $content = json_decode($rawContent, true);

        if ($content === null && ! empty(trim($rawContent))) {
            // If the decoded JSON content is null but the API response body was not empty,
            // it means that there was an error reading the response.
            throw new UnreadableResponseException();
        }

        if ($transformer = $request->getResponseTransformer()) {
            $content = $transformer->transformResponse($content, $request);
        }

        if (! $this->preferences->isEnabled('keep_raw_responses')) {
            $httpResponse = null;
        }

        return new Response($request, $content, $httpResponse);
    }
}
