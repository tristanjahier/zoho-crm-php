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
     * @return Module
     */
    public function module(string $name): Module
    {
        return new Module($this->client, $name);
    }

    /**
     * Get a module helper as a public property.
     *
     * The module name needs to be written in camel case.
     * Example: `$client->records->priceBooks` instead of `$client->records->module('PriceBooks')`.
     *
     * @param string $name The name of the module in camel case
     * @return Module
     */
    public function __get(string $name)
    {
        return $this->module(Inflector::classify($name));
    }
}
