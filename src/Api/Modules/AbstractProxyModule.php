<?php

namespace Zoho\Crm\Api\Modules;

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
     * @param \Zoho\Crm\Client $owner The client to which the module is attached
     * @param string $module The name of the mandator module
     */
    public function __construct($owner, $module)
    {
        parent::__construct($owner);
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
