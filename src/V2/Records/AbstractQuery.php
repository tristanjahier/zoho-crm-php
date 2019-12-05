<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Support\Helper;
use Zoho\Crm\Exceptions\InvalidQueryException;
use Zoho\Crm\V2\AbstractQuery as BaseQuery;

/**
 * Base class for Record APIs queries.
 */
abstract class AbstractQuery extends BaseQuery
{
    /** @var string|null The name of the Zoho module */
    protected $module;

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
     * @return string|null
     */
    public function getModule(): ?string
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
    public function getUri(): string
    {
        $uri = "$this->module";

        if ($this->parameters->isNotEmpty()) {
            $uri .= "?$this->parameters";
        }

        return $uri;
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
