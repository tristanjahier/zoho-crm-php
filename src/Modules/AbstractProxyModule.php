<?php

namespace Zoho\Crm\Modules;

use Zoho\Crm\Client;

/**
 * Base class of the proxy modules.
 *
 * A proxy module makes requests on behalf of an actual API module.
 */
abstract class AbstractProxyModule extends AbstractModule
{
    /** @var string The name of the mandator module */
    protected $mandator;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Client $client The client to which the module is attached
     * @param string $module The name of the mandator module
     */
    public function __construct(Client $client, string $module)
    {
        parent::__construct($client);
        $this->mandator = $module;
    }

    /**
     * Get the name of the mandator module.
     *
     * @return string
     */
    public function mandatorModule()
    {
        return $this->mandator;
    }

    /**
     * @inheritdoc
     */
    public function newQuery($method = null, $params = [], $paginated = false)
    {
        return parent::newQuery($method, $params, $paginated)->module($this->mandator);
    }
}
