<?php

namespace Zoho\Crm\V2;

use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Support\UrlParameters;
use Zoho\Crm\Traits\BasicQueryImplementation;
use Zoho\Crm\Traits\HasHttpVerb;

/**
 * Base class for all API v2 queries.
 */
abstract class AbstractQuery implements QueryInterface
{
    use BasicQueryImplementation, HasHttpVerb;

    /** @var Client The API client that originated this query */
    protected $client;

    /** @var \Zoho\Crm\Support\UrlParameters The URL parameters collection */
    protected $parameters;

    /**
     * The constructor.
     *
     * @param Client $client The client to use to make the request
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->parameters = new UrlParameters();
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setUri(?string $uri)
    {
        $this->parameters = UrlParameters::createFromUrl($uri);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setUriParameter(string $key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }
}
