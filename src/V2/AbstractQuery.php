<?php

namespace Zoho\Crm\V2;

use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Support\UrlParameters;
use Zoho\Crm\Traits\{
    BasicQueryImplementation,
    HasRequestHttpMethod,
    HasRequestUrlParameters
};

/**
 * Base class for all API v2 queries.
 */
abstract class AbstractQuery implements QueryInterface
{
    use BasicQueryImplementation, HasRequestHttpMethod, HasRequestUrlParameters;

    /** @var Client The API client that originated this query */
    protected $client;

    /**
     * The constructor.
     *
     * @param Client $client The client to use to make the request
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->urlParameters = new UrlParameters();
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setUri(?string $uri)
    {
        $this->urlParameters = UrlParameters::createFromUrl($uri);

        return $this;
    }
}
