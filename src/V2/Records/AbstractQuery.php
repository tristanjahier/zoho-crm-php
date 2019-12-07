<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Support\Helper;
use Zoho\Crm\Exceptions\InvalidQueryException;
use Zoho\Crm\V2\AbstractQuery as BaseQuery;
use Zoho\Crm\V2\Client;

/**
 * Base class for Record APIs queries.
 */
abstract class AbstractQuery extends BaseQuery
{
    /** @var string The name of the Zoho module */
    protected $module;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\V2\Client $client The client to use to make the request
     * @param string $module The name of the Zoho module
     */
    public function __construct(Client $client, string $module)
    {
        parent::__construct($client);
        $this->module = $module;
    }

    /**
     * Set the requested module.
     *
     * @param string $module The name of the Zoho module
     * @return $this
     */
    public function setModule(string $module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get the requested module.
     *
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @inheritdoc
     *
     * @return $this
     */
    public function setUri(?string $uri)
    {
        parent::setUri($uri);

        $this->module = Helper::getUrlPathSegmentByIndex($uri, 0);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(): void
    {
        if (is_null($this->module) || empty($this->module)) {
            throw new InvalidQueryException($this, 'the module name must be present.');
        }
    }
}
