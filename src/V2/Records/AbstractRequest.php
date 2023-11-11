<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Exceptions\InvalidRequestException;
use Zoho\Crm\V2\AbstractRequest as BaseRequest;
use Zoho\Crm\V2\Client;

/**
 * Base class for Record APIs requests.
 */
abstract class AbstractRequest extends BaseRequest
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
     */
    public function validate(): void
    {
        if (is_null($this->module) || empty($this->module)) {
            throw new InvalidRequestException($this, 'the module name must be present.');
        }
    }

    /**
     * @inheritdoc
     *
     * @return UnwrapDataTransformer
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface
    {
        return new UnwrapDataTransformer();
    }
}
