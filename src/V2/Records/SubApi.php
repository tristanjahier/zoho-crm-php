<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\V2\AbstractSubApi;
use Zoho\Crm\Support\Helper;

/**
 * Helper for the Record APIs.
 *
 * @property-read ModuleHelper $leads
 * @property-read ModuleHelper $accounts
 * @property-read ModuleHelper $contacts
 * @property-read ModuleHelper $deals
 * @property-read ModuleHelper $campaigns
 * @property-read ModuleHelper $tasks
 * @property-read ModuleHelper $cases
 * @property-read ModuleHelper $events
 * @property-read ModuleHelper $calls
 * @property-read ModuleHelper $solutions
 * @property-read ModuleHelper $products
 * @property-read ModuleHelper $vendors
 * @property-read ModuleHelper $priceBooks
 * @property-read ModuleHelper $quotes
 * @property-read ModuleHelper $salesOrders
 * @property-read ModuleHelper $purchaseOrders
 * @property-read ModuleHelper $invoices
 * @property-read ModuleHelper $activities
 * @property-read ModuleHelper $notes
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
        return $this->module(Helper::inflector()->classify($name));
    }
}
