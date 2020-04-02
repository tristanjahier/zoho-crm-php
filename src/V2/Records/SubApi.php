<?php

namespace Zoho\Crm\V2\Records;

use Doctrine\Common\Inflector\Inflector;
use Zoho\Crm\V2\AbstractSubApi;

/**
 * Helper for the Record APIs.
 */
class SubApi extends AbstractSubApi
{
    /**
     * Create a module helper.
     *
     * @param string $name The name of the module
     * @return ModuleHelper
     */
    public function module(string $name): ModuleHelper
    {
        return new ModuleHelper($this->client, $name);
    }

    /**
     * Get a module helper as a public property.
     *
     * The module name needs to be written in camel case.
     * Example: `$client->records->priceBooks` instead of `$client->records->module('PriceBooks')`.
     *
     * @param string $name The name of the module in camel case
     * @return ModuleHelper
     */
    public function __get(string $name)
    {
        return $this->module(Inflector::classify($name));
    }
}
