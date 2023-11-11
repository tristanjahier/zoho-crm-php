<?php

declare(strict_types=1);

namespace Zoho\Crm\V2;

use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Support\UrlParameters;
use Zoho\Crm\Traits\{
    BasicRequestImplementation,
    HasRequestHttpMethod,
    HasRequestUrlParameters
};

/**
 * Base class for all API v2 requests.
 */
abstract class AbstractRequest implements RequestInterface
{
    use BasicRequestImplementation, HasRequestHttpMethod, HasRequestUrlParameters;

    /** @var Client The API client that originated this request */
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
     * Allow the deep cloning of the request.
     *
     * @return void
     */
    public function __clone()
    {
        $this->urlParameters = clone $this->urlParameters;
    }
}
